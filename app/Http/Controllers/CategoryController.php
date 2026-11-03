<?php

namespace App\Http\Controllers;

use App\Models\Category;
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

class CategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->string('search')->trim()->toString();
        $institution = $request->string('institution_id')->toString();

        return Inertia::render('Categories/Index', [
            'categories' => Category::query()->with('institution:id,name')
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$search.'%')))
                ->when($institution !== '', fn ($query) => $query->where('institution_id', $institution))
                ->orderBy('name')->paginate(10)->withQueryString(),
            'institutions' => Institution::query()->orderBy('name')->get(['id', 'name']),
            'filters' => ['search' => $search, 'institution_id' => $institution],
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $category = new Category;
        $category->forceFill($this->validated($request))->save();

        return to_route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        Gate::authorize('update', $category);
        $category->forceFill($this->validated($request, $category))->save();

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        Gate::authorize('delete', $category);
        DB::transaction(function () use ($category): void {
            $category = Category::query()->lockForUpdate()->whereKey($category->getKey())->firstOrFail();
            if ($category->items()->exists()) {
                throw ValidationException::withMessages(['category' => 'Kategori masih memiliki barang. Hapus atau pindahkan barang terkait terlebih dahulu.']);
            }
            $category->delete();
        });

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?Category $category = null): array
    {
        $actor = $request->user();
        abort_unless($actor instanceof User, 403);

        return $request->validate([
            'institution_id' => [
                'required', 'integer', Rule::exists('institutions', 'id'),
                ...($actor->role === 'superadmin' ? [] : [Rule::in([$actor->institution_id])]),
                ...($category === null ? [] : [Rule::in([$category->institution_id])]),
            ],
            'name' => ['required', 'string', 'max:100', Rule::unique('categories', 'name')
                ->where('institution_id', $request->input('institution_id'))->ignore($category)],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
    }
}
