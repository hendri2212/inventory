<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        return match ($request->query('menu')) {
            'barang' => to_route('items.index'),
            'unit' => to_route('item-units.index'),
            'lokasi' => to_route('rooms.index'),
            'laporan' => to_route('reports.index'),
            default => $this->dashboard($request),
        };
    }

    private function dashboard(Request $request): Response
    {
        $user = $request->user();
        abort_unless($user instanceof User, 403);

        $units = ItemUnit::query();
        $onLoan = fn ($items) => $items->whereNull('returned_at');
        $canViewUsers = $user->can('viewAny', User::class);

        return Inertia::render('Welcome', [
            'scope' => [
                'institution' => $user->institution?->name,
                'department' => $user->department?->name,
            ],
            'summary' => [
                'total_units' => (clone $units)->count(),
                'total_purchase_value' => (string) (clone $units)->sum('purchase_price'),
                'loaned_units' => (clone $units)->whereHas('loanItems', $onLoan)->count(),
                'available_units' => (clone $units)->whereNull('disposed_at')
                    ->where(fn ($query) => $query->whereNull('condition')->orWhere('condition', '!=', 'major_damage'))
                    ->whereHas('department', fn ($department) => $department->where('is_active', true))
                    ->whereHas('institution', fn ($institution) => $institution->where('is_active', true))
                    ->whereDoesntHave('loanItems', $onLoan)->count(),
                'total_users' => $canViewUsers ? User::query()->visibleTo($user)->count() : null,
                'item_types' => $canViewUsers ? null : (clone $units)->distinct()->count('item_id'),
            ],
            'loans' => Loan::query()
                ->whereHas('items.itemUnit')
                ->with(['items' => fn ($items) => $items->whereHas('itemUnit')->with('itemUnit.item')])
                ->orderByDesc('borrowed_at')->orderByDesc('id')->limit(5)->get()
                ->map(fn (Loan $loan) => [
                    'id' => $loan->id,
                    'code' => $loan->getAttribute('code'),
                    'borrower_name' => $loan->getAttribute('borrower_name'),
                    'borrower_department' => $loan->getAttribute('borrower_department'),
                    'borrowed_at' => $loan->getAttribute('borrowed_at')->timezone('Asia/Makassar')->format('Y-m-d'),
                    'unit_names' => $loan->items->map(fn ($item) => $item->itemUnit?->item?->name)
                        ->filter()->unique()->values(),
                    'unit_count' => $loan->items->count(),
                    'returned_count' => $loan->items->whereNotNull('returned_at')->count(),
                ]),
        ]);
    }
}
