<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\ItemUnit;
use App\Models\Loan;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class LoanController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Loan::class);
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:150'], 'status' => ['nullable', Rule::in(['active', 'partial', 'completed', 'overdue'])]]);
        $query = Loan::query()->with('department:id,name')->withCount(['items', 'items as returned_count' => fn ($q) => $q->whereNotNull('returned_at')]);
        $search = trim($filters['search'] ?? '');
        $query->when($search !== '', fn ($q) => $q->where(fn ($q) => $q->where('code', 'like', '%'.$search.'%')->orWhere('borrower_name', 'like', '%'.$search.'%')));
        match ($filters['status'] ?? '') {
            'active' => $query->whereDoesntHave('items', fn ($q) => $q->whereNotNull('returned_at'))->whereHas('items'),
            'partial' => $query->whereHas('items', fn ($q) => $q->whereNotNull('returned_at'))->whereHas('items', fn ($q) => $q->whereNull('returned_at')),
            'completed' => $query->whereDoesntHave('items', fn ($q) => $q->whereNull('returned_at')),
            'overdue' => $query->where('due_at', '<', now())->whereHas('items', fn ($q) => $q->whereNull('returned_at')),
            default => null,
        };

        return Inertia::render('Loans/Index', [
            'loans' => $query->latest('id')->paginate(10)->withQueryString(),
            'departments' => Department::query()->where('is_active', true)->whereHas('institution', fn ($q) => $q->where('is_active', true))->with('institution:id,name')->orderBy('name')->get(['id', 'institution_id', 'name']),
            'units' => ItemUnit::query()->whereDoesntHave('loanItems', fn ($q) => $q->whereNull('returned_at'))
                ->where(fn ($q) => $q->whereNull('condition')->orWhere('condition', '!=', 'major_damage'))
                ->with('item:id,name')->orderBy('code')->get(['id', 'institution_id', 'item_id', 'department_id', 'code', 'serial_number', 'condition']),
            'filters' => $filters,
            'success' => $request->session()->get('success'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Loan::class);
        $data = $request->validate([
            'department_id' => ['required', 'integer'],
            'borrower_name' => ['required', 'string', 'max:150'],
            'borrower_identifier' => ['nullable', 'string', 'max:100'],
            'borrower_department' => ['nullable', 'string', 'max:150'],
            'borrower_contact' => ['nullable', 'string', 'max:100'],
            'borrowed_at' => ['required', 'date_format:Y-m-d\TH:i'],
            'due_at' => ['nullable', 'date_format:Y-m-d\TH:i', 'after_or_equal:borrowed_at'],
            'notes' => ['required', 'string', 'max:5000'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['array:item_unit_id,condition_out,notes_out'],
            'items.*.item_unit_id' => ['required', 'integer', 'distinct'],
            'items.*.condition_out' => ['required', Rule::in(['good', 'minor_damage'])],
            'items.*.notes_out' => ['nullable', 'string', 'max:5000'],
        ]);
        if (CarbonImmutable::parse($data['borrowed_at'], 'Asia/Makassar')->isFuture()) {
            throw ValidationException::withMessages(['borrowed_at' => 'Tanggal pinjam tidak boleh di masa depan.']);
        }
        $department = Department::query()->where('is_active', true)->whereHas('institution', fn ($q) => $q->where('is_active', true))->whereKey($data['department_id'])->firstOrFail();
        $loan = DB::transaction(function () use ($data, $department, $request): Loan {
            // Serialize numbering and lending within the institution.
            DB::table('institutions')->where('id', $department->institution_id)->lockForUpdate()->first();
            $ids = array_column($data['items'], 'item_unit_id');
            sort($ids);
            $units = ItemUnit::query()->where('department_id', $department->id)->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            if ($units->count() !== count($ids) || $units->contains(fn ($u) => $u->condition === 'major_damage' || $u->loanItems()->whereNull('returned_at')->exists())) {
                throw ValidationException::withMessages(['items' => 'Ada unit tidak tersedia, rusak berat, atau berasal dari bidang lain.']);
            }
            $prefix = $department->code.'-PJM-';
            $max = 0;
            foreach (DB::table('loans')->where('institution_id', $department->institution_id)->pluck('code') as $code) {
                if (preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $code, $m)) {
                    $max = max($max, (int) $m[1]);
                }
            }
            $code = $prefix.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
            if (mb_strlen($code) > 50) {
                throw ValidationException::withMessages(['department_id' => 'Kode bidang terlalu panjang.']);
            }
            $loan = new Loan;
            $loan->forceFill([
                ...Arr::except($data, ['items', 'borrowed_at', 'due_at']),
                'institution_id' => $department->institution_id, 'code' => $code, 'recorded_by' => $request->user()->id,
                'borrowed_at' => CarbonImmutable::parse($data['borrowed_at'], 'Asia/Makassar')->utc(),
                'due_at' => empty($data['due_at']) ? null : CarbonImmutable::parse($data['due_at'], 'Asia/Makassar')->utc(),
            ])->save();
            foreach ($data['items'] as $item) {
                $loan->items()->make()->forceFill([...$item, 'loan_id' => $loan->id, 'institution_id' => $department->institution_id, 'active_item_unit_id' => $item['item_unit_id']])->save();
                $units->firstWhere('id', $item['item_unit_id'])->forceFill(['condition' => $item['condition_out']])->save();
            }

            return $loan;
        });

        return to_route('loans.show', $loan)->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function show(Request $request, Loan $loan): Response
    {
        Gate::authorize('view', $loan);

        return Inertia::render('Loans/Show', [
            'loan' => $loan->load(['department:id,name', 'institution:id,name,address', 'recorder:id,name', 'items.itemUnit.item:id,name', 'items.returner:id,name']),
            'success' => $request->session()->get('success'),
        ]);
    }

    public function returnItems(Request $request, Loan $loan): RedirectResponse
    {
        Gate::authorize('update', $loan);
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*' => ['array:id,condition_in,notes_in'],
            'items.*.id' => ['required', 'integer', 'distinct'],
            'items.*.condition_in' => ['required', Rule::in(['good', 'minor_damage', 'major_damage'])],
            'items.*.notes_in' => ['nullable', 'string', 'max:5000'],
        ]);
        DB::transaction(function () use ($loan, $data, $request): void {
            DB::table('institutions')->where('id', $loan->institution_id)->lockForUpdate()->first();
            foreach ($data['items'] as $entry) {
                $item = $loan->items()->whereKey($entry['id'])->lockForUpdate()->first();
                if (! $item || $item->returned_at !== null) {
                    throw ValidationException::withMessages(['items' => 'Unit tidak valid atau sudah dikembalikan.']);
                }
                $unit = ItemUnit::query()->whereKey($item->item_unit_id)->lockForUpdate()->firstOrFail();
                $item->forceFill([...Arr::except($entry, ['id']), 'active_item_unit_id' => null, 'returned_at' => now(), 'returned_by' => $request->user()->id])->save();
                $unit->forceFill(['condition' => $entry['condition_in'], 'condition_notes' => $entry['notes_in'] ?? null])->save();
            }
        });

        return back()->with('success', 'Pengembalian berhasil dicatat.');
    }
}
