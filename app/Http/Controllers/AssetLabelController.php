<?php

namespace App\Http\Controllers;

use App\Models\ItemUnit;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class AssetLabelController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', ItemUnit::class);

        return Inertia::render('AssetLabels/Index', [
            'units' => ItemUnit::query()->with(['item:id,name', 'institution:id,name', 'department:id,name'])
                ->orderBy('code')->orderBy('id')->get(['id', 'institution_id', 'item_id', 'department_id', 'code', 'purchase_year', 'funding_source'])
                ->map(fn (ItemUnit $unit) => [
                    'id' => $unit->id,
                    'code' => $unit->code,
                    'item' => $unit->item?->name,
                    'institution' => $unit->institution?->name,
                    'department' => $unit->department?->name,
                    'purchase_year' => $unit->purchase_year,
                    'funding_source' => $unit->funding_source,
                ]),
        ]);
    }
}
