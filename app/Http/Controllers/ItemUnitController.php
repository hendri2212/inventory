<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemUnitRequest;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemUnitController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', ItemUnit::class);
        $search = $request->string('search')->trim()->toString();

        return Inertia::render('ItemUnits/Index', [
            'units' => ItemUnit::query()->with(['item:id,name', 'institution:id,name', 'department:id,name', 'room:id,name'])
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('code', 'like', '%'.$search.'%')
                    ->orWhere('serial_number', 'like', '%'.$search.'%')->orWhereHas('item', fn ($item) => $item->where('name', 'like', '%'.$search.'%'))))
                ->when($request->filled('item_id'), fn ($query) => $query->where('item_id', $request->input('item_id')))
                ->orderBy('code')->paginate(10)->withQueryString()->through(fn (ItemUnit $unit) => [
                    ...$unit->toArray(),
                    'disposed_at' => $unit->getAttribute('disposed_at')?->format('Y-m-d'),
                    'photo_url' => $unit->getAttribute('photo_path') ? route('item-units.photo', $unit) : null,
                ]),
            'items' => Item::query()->orderBy('name')->get(['id', 'name', 'institution_id']),
            'institutions' => Institution::query()->orderBy('name')->get(['id', 'name']),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name', 'institution_id']),
            'rooms' => Room::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'institution_id']),
            'filters' => $request->only(['search', 'item_id']),
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(SaveItemUnitRequest $request): RedirectResponse
    {
        $this->save($request, new ItemUnit);

        return to_route('item-units.index')->with('success', 'Unit aset berhasil ditambahkan.');
    }

    public function update(SaveItemUnitRequest $request, ItemUnit $itemUnit): RedirectResponse
    {
        $this->save($request, $itemUnit);

        return back()->with('success', 'Unit aset berhasil diperbarui.');
    }

    public function destroy(ItemUnit $itemUnit): RedirectResponse
    {
        Gate::authorize('delete', $itemUnit);
        $path = $itemUnit->getAttribute('photo_path');
        DB::transaction(function () use ($itemUnit): void {
            $unit = ItemUnit::query()->lockForUpdate()->whereKey($itemUnit->getKey())->firstOrFail();
            if (DB::table('loan_items')->where('item_unit_id', $unit->getKey())->exists()
                || DB::table('item_transfer_items')->where('item_unit_id', $unit->getKey())->exists()) {
                throw ValidationException::withMessages(['unit' => 'Unit memiliki riwayat transaksi. Nonaktifkan unit sebagai gantinya.']);
            }
            $unit->delete();
        });
        if ($path) {
            Storage::disk('local')->delete($path);
        }

        return back()->with('success', 'Unit aset berhasil dihapus.');
    }

    public function photo(ItemUnit $itemUnit): StreamedResponse
    {
        Gate::authorize('view', $itemUnit);
        $path = $itemUnit->getAttribute('photo_path');
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, null, ['Cache-Control' => 'private, no-store']);
    }

    private function save(SaveItemUnitRequest $request, ItemUnit $unit): void
    {
        $data = $request->safe()->except(['photo', 'remove_photo']);
        $oldPath = $unit->getAttribute('photo_path');
        $newPath = $request->hasFile('photo') ? $request->file('photo')->store('unit-photos', 'local') : null;
        if ($newPath || $request->boolean('remove_photo')) {
            $data['photo_path'] = $newPath;
        }
        try {
            DB::transaction(function () use ($unit, $data): void {
                if ($unit->exists) {
                    $unit = ItemUnit::query()->lockForUpdate()->whereKey($unit->getKey())->firstOrFail();
                    if ($unit->loanItems()->whereNull('returned_at')->exists()) {
                        throw ValidationException::withMessages(['unit' => 'Unit sedang dipinjam. Selesaikan pengembalian sebelum mengubah data unit.']);
                    }
                }
                $unit->forceFill($data)->save();
            });
        } catch (\Throwable $error) {
            if ($newPath) {
                Storage::disk('local')->delete($newPath);
            }
            throw $error;
        }
        if ($oldPath && ($newPath || $request->boolean('remove_photo'))) {
            Storage::disk('local')->delete($oldPath);
        }
    }
}
