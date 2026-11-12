<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\Room;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class InventoryReportController extends Controller
{
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Item::class);
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'institution_id' => ['nullable', 'integer', 'min:1'],
            'category_id' => ['nullable', 'integer', 'min:1'],
            'department_id' => ['nullable', 'integer', 'min:1'],
            'room_id' => ['nullable', 'integer', 'min:1'],
            'condition' => ['nullable', Rule::in(['good', 'minor_damage', 'major_damage', 'unknown'])],
            'print' => ['sometimes', 'boolean'],
        ]);
        $search = trim($filters['search'] ?? '');
        $unitFilter = function (Builder $query) use ($filters): void {
            foreach (['department_id', 'room_id'] as $field) {
                if (! empty($filters[$field])) {
                    $query->where($field, $filters[$field]);
                }
            }
            if (($filters['condition'] ?? null) === 'unknown') {
                $query->whereNull('condition');
            } elseif (! empty($filters['condition'])) {
                $query->where('condition', $filters['condition']);
            }
        };
        $query = Item::query()->with(['institution:id,name', 'category:id,name'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('name', 'like', '%'.$search.'%')->orWhere('code', 'like', '%'.$search.'%')))
            ->when(! empty($filters['institution_id']), fn ($query) => $query->where('institution_id', $filters['institution_id']))
            ->when(! empty($filters['category_id']), fn ($query) => $query->where('category_id', $filters['category_id']))
            ->whereHas('units', $unitFilter)
            ->withCount(['units' => $unitFilter])
            ->with(['units' => function ($query) use ($unitFilter): void {
                $unitFilter($query->getQuery());
                $query->with(['department:id,name', 'room:id,name'])->orderBy('code');
            }])->orderBy('name')->orderBy('id');
        $printing = $request->boolean('print');
        if ($printing) {
            $paginator = null;
            $items = $query->get();
        } else {
            $paginator = $query->paginate(10)->withQueryString();
            $items = $paginator->getCollection();
        }

        return Inertia::render('Reports/Index', [
            'items' => $items->map(fn (Item $item) => [
                'id' => $item->id,
                'code' => $item->getAttribute('code'),
                'name' => $item->getAttribute('name'),
                'description' => $item->getAttribute('description'),
                'unit' => $item->getAttribute('unit'),
                'institution' => $item->institution?->name,
                'category' => $item->category?->name,
                'units_count' => $item->getAttribute('units_count'),
                'units' => $item->units->map(fn ($unit) => [
                    'id' => $unit->id,
                    'code' => $unit->getAttribute('code'),
                    'serial_number' => $unit->getAttribute('serial_number'),
                    'description' => $unit->getAttribute('description'),
                    'department' => $unit->department?->name,
                    'room' => $unit->room?->name,
                    'purchase_year' => $unit->getAttribute('purchase_year'),
                    'purchase_price' => $unit->getAttribute('purchase_price'),
                    'funding_source' => $unit->getAttribute('funding_source'),
                    'condition' => $unit->getAttribute('condition'),
                    'condition_notes' => $unit->getAttribute('condition_notes'),
                ]),
            ]),
            'pagination' => $paginator ? ['total' => $paginator->total(), 'links' => $paginator->linkCollection()] : null,
            'institutions' => Institution::query()->orderBy('name')->get(['id', 'name']),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'institution_id']),
            'departments' => Department::query()->orderBy('name')->get(['id', 'name', 'institution_id']),
            'rooms' => Room::query()->orderBy('name')->get(['id', 'name', 'institution_id']),
            'filters' => $request->only(['search', 'institution_id', 'category_id', 'department_id', 'room_id', 'condition']),
            'printing' => $printing,
            'generated_at' => now()->timezone('Asia/Makassar')->format('d/m/Y H:i').' WITA',
        ]);
    }
}
