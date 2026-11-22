<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Institution;
use App\Models\ItemTransfer;
use App\Models\ItemUnit;
use App\Models\Room;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ItemTransferController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', ItemTransfer::class);
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:150'], 'type' => ['nullable', Rule::in(['room', 'department'])]]);
        $search = trim($filters['search'] ?? '');
        $canCreate = $request->user()->can('create', ItemTransfer::class);
        $transfers = ItemTransfer::query()->with(['institution:id,name', 'fromRoom:id,name', 'toRoom:id,name'])->withCount('items')
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('code', 'like', '%'.$search.'%')->orWhere('notes', 'like', '%'.$search.'%')))
            ->when(! empty($filters['type']), fn ($q) => $q->where('type', $filters['type']))
            ->latest('id')->paginate(10)->withQueryString();
        $transfers->through(fn (ItemTransfer $transfer) => [...$transfer->toArray(), ...$this->departmentNames($transfer)]);

        return Inertia::render('ItemTransfers/Index', [
            'transfers' => $transfers,
            'can_create' => $canCreate,
            'institutions' => $canCreate ? Institution::query()->where('is_active', true)->orderBy('name')->get(['id', 'code', 'name']) : [],
            'departments' => $canCreate ? Department::query()->where('is_active', true)->whereHas('institution', fn ($q) => $q->where('is_active', true))->orderBy('name')->get(['id', 'institution_id', 'name']) : [],
            'rooms' => $canCreate ? Room::query()->orderBy('name')->get(['id', 'institution_id', 'name', 'is_active']) : [],
            'units' => $canCreate ? ItemUnit::query()->with('item:id,name')->whereDoesntHave('loanItems', fn ($q) => $q->whereNull('returned_at'))
                ->orderBy('code')->get(['id', 'institution_id', 'item_id', 'department_id', 'room_id', 'code', 'serial_number']) : [],
            'filters' => $filters,
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', ItemTransfer::class);
        $data = $request->validate([
            'institution_id' => ['required', 'integer'],
            'type' => ['required', Rule::in(['room', 'department'])],
            'from_department_id' => ['required', 'integer'], 'to_department_id' => ['required', 'integer'],
            'from_room_id' => ['nullable', 'integer'], 'to_room_id' => ['nullable', 'integer'],
            'transferred_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'notes' => ['required', 'string', 'max:5000'],
            'unit_ids' => ['required', 'array', 'min:1', 'max:100'],
            'unit_ids.*' => ['required', 'integer', 'distinct'],
        ]);
        $institution = Institution::query()->where('is_active', true)->whereKey($data['institution_id'])->firstOrFail();
        foreach (['from_department_id', 'to_department_id'] as $field) {
            if (! Department::query()->where('institution_id', $institution->id)->where('is_active', true)->whereKey($data[$field])->exists()) {
                throw ValidationException::withMessages([$field => 'Bidang harus aktif dan berada dalam instansi yang dipilih.']);
            }
        }
        foreach (['from_room_id', 'to_room_id'] as $field) {
            if (! empty($data[$field]) && ! Room::query()->where('institution_id', $institution->id)->when($field === 'to_room_id', fn ($q) => $q->where('is_active', true))->whereKey($data[$field])->exists()) {
                throw ValidationException::withMessages([$field => 'Ruang tidak valid untuk instansi ini atau tujuan sudah nonaktif.']);
            }
        }
        if ($data['type'] === 'room' && $data['from_department_id'] != $data['to_department_id']) {
            throw ValidationException::withMessages(['to_department_id' => 'Mutasi ruang harus tetap pada bidang yang sama.']);
        }
        if ($data['type'] === 'department' && $data['from_department_id'] == $data['to_department_id']) {
            throw ValidationException::withMessages(['to_department_id' => 'Pilih bidang tujuan yang berbeda.']);
        }
        if ($data['type'] === 'room' && ($data['from_room_id'] ?? null) == ($data['to_room_id'] ?? null)) {
            throw ValidationException::withMessages(['to_room_id' => 'Pilih ruang tujuan yang berbeda.']);
        }
        $time = CarbonImmutable::parse($data['transferred_at'], 'Asia/Makassar');
        if ($time->isFuture()) {
            throw ValidationException::withMessages(['transferred_at' => 'Tanggal mutasi tidak boleh di masa depan.']);
        }
        $transfer = DB::transaction(function () use ($data, $institution, $time, $request): ItemTransfer {
            DB::table('institutions')->where('id', $institution->id)->lockForUpdate()->first();
            $units = ItemUnit::query()->where('institution_id', $institution->id)->whereIn('id', $data['unit_ids'])->orderBy('id')->lockForUpdate()->get();
            if ($units->count() !== count($data['unit_ids']) || $units->contains(fn ($u) => $u->department_id != $data['from_department_id'] || $u->room_id != ($data['from_room_id'] ?? null) || $u->loanItems()->whereNull('returned_at')->exists())) {
                throw ValidationException::withMessages(['unit_ids' => 'Ada unit sedang dipinjam atau penempatan asal sudah berubah. Muat ulang daftar dan pilih kembali.']);
            }
            // Reject backdated transfers that would reorder an existing unit history.
            if (DB::table('item_transfer_items')->join('item_transfers', 'item_transfer_items.item_transfer_id', '=', 'item_transfers.id')->whereIn('item_unit_id', $data['unit_ids'])->where('transferred_at', '>', $time->utc())->exists()) {
                throw ValidationException::withMessages(['transferred_at' => 'Tanggal mutasi tidak boleh mendahului riwayat mutasi terakhir unit.']);
            }
            $prefix = $institution->code.'-MUT-';
            $max = 0;
            foreach (DB::table('item_transfers')->where('institution_id', $institution->id)->pluck('code') as $code) {
                if (preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $match)) {
                    $max = max($max, (int) $match[1]);
                }
            }
            $code = $prefix.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
            if (mb_strlen($code) > 50) {
                throw ValidationException::withMessages(['institution_id' => 'Kode instansi terlalu panjang.']);
            }
            $transfer = new ItemTransfer;
            $transfer->forceFill([
                'institution_id' => $institution->id, 'code' => $code, 'type' => $data['type'],
                'from_department_id' => $data['from_department_id'], 'to_department_id' => $data['to_department_id'],
                'from_room_id' => $data['from_room_id'] ?? null, 'to_room_id' => $data['to_room_id'] ?? null,
                'transferred_at' => $time->utc(), 'notes' => $data['notes'], 'recorded_by' => $request->user()->id,
            ])->save();
            foreach ($units as $unit) {
                $transfer->items()->make()->forceFill(['institution_id' => $institution->id, 'item_transfer_id' => $transfer->id, 'item_unit_id' => $unit->id])->save();
                $unit->forceFill(['department_id' => $data['to_department_id'], 'room_id' => $data['to_room_id'] ?? null])->save();
            }

            return $transfer;
        });

        return to_route('item-transfers.show', $transfer)->with('success', 'Mutasi berhasil dicatat dan penempatan unit diperbarui.');
    }

    public function show(Request $request, ItemTransfer $itemTransfer): Response
    {
        Gate::authorize('view', $itemTransfer);
        // Authorize the transaction first. Unit details in its history remain readable
        // to the source department after the units have moved to another department.
        $unitIds = DB::table('item_transfer_items')->where('item_transfer_id', $itemTransfer->id)->where('institution_id', $itemTransfer->institution_id)->pluck('item_unit_id');
        $units = DB::table('item_units')->join('items', 'items.id', '=', 'item_units.item_id')->where('item_units.institution_id', $itemTransfer->institution_id)->whereIn('item_units.id', $unitIds)
            ->orderBy('item_units.code')->get(['item_units.id', 'item_units.code', 'item_units.serial_number', 'items.name']);

        return Inertia::render('ItemTransfers/Show', [
            'transfer' => [...$itemTransfer->load(['institution:id,name,address', 'fromRoom:id,name', 'toRoom:id,name', 'recorder:id,name'])->toArray(), ...$this->departmentNames($itemTransfer)],
            'units' => $units,
            'success' => $request->session()->get('success'),
        ]);
    }

    /** @return array{from_department_name: ?string, to_department_name: ?string} */
    private function departmentNames(ItemTransfer $transfer): array
    {
        // Both department names belong to an already authorized transaction.
        $names = DB::table('departments')->where('institution_id', $transfer->institution_id)->whereIn('id', [$transfer->from_department_id, $transfer->to_department_id])->pluck('name', 'id');

        return ['from_department_name' => $names[$transfer->from_department_id] ?? null, 'to_department_name' => $names[$transfer->to_department_id] ?? null];
    }
}
