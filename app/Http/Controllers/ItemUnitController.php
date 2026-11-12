<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveItemUnitRequest;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\ItemUnitPhoto;
use App\Models\Room;
use App\Support\InventoryCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
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
            'units' => ItemUnit::query()->with(['item:id,name', 'institution:id,name', 'department:id,name', 'room:id,name', 'photos'])
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('code', 'like', '%'.$search.'%')
                    ->orWhere('serial_number', 'like', '%'.$search.'%')->orWhereHas('item', fn ($item) => $item->where('name', 'like', '%'.$search.'%'))))
                ->when($request->filled('item_id'), fn ($query) => $query->where('item_id', $request->input('item_id')))
                ->orderBy('code')->paginate(10)->withQueryString()->through(fn (ItemUnit $unit) => [
                    ...$unit->toArray(),
                    'photos' => $unit->photos->map(fn (ItemUnitPhoto $photo) => [
                        'id' => $photo->id,
                        'url' => route('item-units.photo', [$unit, $photo->id]),
                    ]),
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
        $paths = DB::transaction(function () use ($itemUnit): array {
            $unit = ItemUnit::query()->lockForUpdate()->whereKey($itemUnit->getKey())->firstOrFail();
            if (DB::table('loan_items')->where('item_unit_id', $unit->getKey())->exists()
                || DB::table('item_transfer_items')->where('item_unit_id', $unit->getKey())->exists()) {
                throw ValidationException::withMessages(['unit' => 'Unit memiliki riwayat transaksi dan tidak dapat dihapus.']);
            }
            $paths = $unit->photos()->pluck('path')->all();
            $unit->delete();

            return $paths;
        });
        Storage::disk('local')->delete($paths);

        return back()->with('success', 'Unit aset berhasil dihapus.');
    }

    public function photo(ItemUnit $itemUnit, ?int $photo = null): StreamedResponse
    {
        Gate::authorize('view', $itemUnit);
        $record = $photo === null ? $itemUnit->photos()->firstOrFail() : $itemUnit->photos()->findOrFail($photo);
        abort_unless(Storage::disk('local')->exists($record->path), 404);

        return Storage::disk('local')->response($record->path, null, ['Cache-Control' => 'private, no-store']);
    }

    private function save(SaveItemUnitRequest $request, ItemUnit $unit): void
    {
        $data = $request->safe()->except(['photos', 'remove_photo_ids']);
        $removeIds = $request->validated('remove_photo_ids', []);
        $newPaths = [];
        try {
            /** @var list<UploadedFile> $uploads */
            $uploads = $request->validated('photos', []);
            foreach ($uploads as $upload) {
                $path = $upload->store('unit-photos', 'local');
                if ($path === false) {
                    throw ValidationException::withMessages(['photos' => 'Foto gagal disimpan.']);
                }
                $newPaths[] = $path;
            }
            $oldPaths = DB::transaction(function () use ($unit, $data, $newPaths, $removeIds): array {
                if ($unit->exists) {
                    $unit = ItemUnit::query()->lockForUpdate()->whereKey($unit->getKey())->firstOrFail();
                    if ($unit->loanItems()->whereNull('returned_at')->exists()) {
                        throw ValidationException::withMessages(['unit' => 'Unit sedang dipinjam. Selesaikan pengembalian sebelum mengubah data unit.']);
                    }
                }
                if (! $unit->exists) {
                    $data['code'] = InventoryCode::next('item_units', $data['institution_id'], $data['department_id']);
                }
                $unit->forceFill($data)->save();
                $removed = $unit->photos()->getQuery()->reorder()->whereKey($removeIds);
                $oldPaths = $removed->pluck('path')->all();
                $removed->delete();
                foreach ($newPaths as $path) {
                    $unit->photos()->create(['path' => $path]);
                }

                return $oldPaths;
            });
        } catch (\Throwable $error) {
            Storage::disk('local')->delete($newPaths);
            throw $error;
        }
        Storage::disk('local')->delete($oldPaths);
    }
}
