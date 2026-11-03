<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Institution;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $institution = $request->string('institution_id')->toString();

        return Inertia::render('Departments/Index', [
            'departments' => Department::query()->with('institution:id,name')
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('code', 'like', '%'.$search.'%')->orWhere('name', 'like', '%'.$search.'%')))
                ->when($institution !== '', fn ($query) => $query->where('institution_id', $institution))
                ->orderBy('name')->paginate(10)->withQueryString(),
            'institutions' => Institution::query()->orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search, 'institution_id' => $institution],
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $department = new Department;
        $department->forceFill($this->validated($request))->save();

        return to_route('departments.index')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        Gate::authorize('update', $department);
        $department->forceFill($this->validated($request, $department))->save();

        return back()->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        Gate::authorize('delete', $department);
        DB::transaction(function () use ($department): void {
            $department = Department::query()->lockForUpdate()->whereKey($department->getKey())->firstOrFail();
            if ($department->units()->exists() || User::where('department_id', $department->getKey())->exists()
                || DB::table('loans')->where('department_id', $department->getKey())->exists()
                || DB::table('item_transfers')->where('from_department_id', $department->getKey())->orWhere('to_department_id', $department->getKey())->exists()) {
                throw ValidationException::withMessages(['department' => 'Bidang masih memiliki pengguna, unit aset, atau riwayat transaksi. Nonaktifkan bidang atau hapus data terkait terlebih dahulu.']);
            }
            $department->delete();
        });

        return back()->with('success', 'Bidang berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Department $department = null): array
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        return $request->validate([
            'institution_id' => [
                'required', 'integer', Rule::exists('institutions', 'id'),
                ...($actor->role === 'superadmin' ? [] : [Rule::in([$actor->institution_id])]),
                ...($department === null ? [] : [Rule::in([$department->institution_id])]),
            ],
            'code' => ['required', 'string', 'max:50', Rule::unique('departments', 'code')
                ->where('institution_id', $request->input('institution_id'))->ignore($department)],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
