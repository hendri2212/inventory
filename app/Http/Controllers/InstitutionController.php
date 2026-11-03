<?php

namespace App\Http\Controllers;

use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class InstitutionController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();

        return Inertia::render('Institutions/Index', [
            'institutions' => Institution::query()
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('code', 'like', '%'.$search.'%')->orWhere('name', 'like', '%'.$search.'%')))
                ->orderBy('name')->paginate(10)->withQueryString(),
            'filters' => ['search' => $search],
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Institution::create($this->validated($request));

        return to_route('institutions.index')->with('success', 'Instansi berhasil ditambahkan.');
    }

    public function update(Request $request, Institution $institution): RedirectResponse
    {
        $institution->update($this->validated($request, $institution));

        return back()->with('success', 'Instansi berhasil diperbarui.');
    }

    public function destroy(Institution $institution): RedirectResponse
    {
        DB::transaction(function () use ($institution): void {
            $institution = Institution::query()->lockForUpdate()->whereKey($institution->getKey())->firstOrFail();
            if ($institution->departments()->exists() || $institution->categories()->exists()
                || $institution->rooms()->exists() || $institution->items()->exists() || User::where('institution_id', $institution->getKey())->exists()) {
                throw ValidationException::withMessages(['institution' => 'Instansi masih memiliki data terkait. Nonaktifkan instansi atau hapus data terkait terlebih dahulu.']);
            }
            $institution->delete();
        });

        return back()->with('success', 'Instansi berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Institution $institution = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:50', Rule::unique('institutions', 'code')->ignore($institution)],
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
