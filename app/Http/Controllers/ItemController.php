<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemRequest;
use App\Models\Category;
use App\Models\Institution;
use App\Models\Item;
use App\Support\InventoryCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ItemController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Item::class);
        $filters = $request->only(['search', 'institution_id', 'category_id']);
        $search = $request->string('search')->trim()->toString();

        return Inertia::render('Items/Index', [
            'items' => Item::query()->with(['institution:id,name', 'category:id,name'])->withCount('units')
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('name', 'like', '%'.$search.'%')->orWhere('code', 'like', '%'.$search.'%')))
                ->when($request->filled('institution_id'), fn ($query) => $query->where('institution_id', $request->input('institution_id')))
                ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->input('category_id')))
                ->orderBy('name')->paginate(10)->withQueryString()->through(fn (Item $item) => [
                    ...$item->toArray(),
                    'can_update' => $request->user()?->can('update', $item),
                    'can_delete' => $request->user()?->can('delete', $item),
                ]),
            'institutions' => Institution::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'institution_id']),
            'can_create' => $request->user()?->can('create', Item::class),
            'filters' => $filters,
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(SaveItemRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $data['code'] = InventoryCode::next('items', $data['institution_id']);
            (new Item)->forceFill($data)->save();
        });

        return to_route('items.index')->with('success', 'Barang berhasil ditambahkan.');
    }

    public function update(SaveItemRequest $request, Item $item): RedirectResponse
    {
        $item->forceFill($request->validated())->save();

        return back()->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item): RedirectResponse
    {
        Gate::authorize('delete', $item);
        if ($item->units()->exists()) {
            throw ValidationException::withMessages(['item' => 'Barang masih memiliki unit aset.']);
        }
        $item->delete();

        return back()->with('success', 'Barang berhasil dihapus.');
    }
}
