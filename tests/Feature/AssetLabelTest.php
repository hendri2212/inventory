<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('label page requires authentication', function () {
    $this->get('/asset-labels')->assertRedirect('/login');
});

test('label page only shows units visible to the operator', function () {
    [$a, , $departments] = $fixture = unitFixture();
    $data = unitPayload($fixture);
    DB::table('item_units')->insert([...$data, 'code' => 'LABEL-1']);
    DB::table('item_units')->insert([...$data, 'code' => 'LABEL-2', 'department_id' => $departments[1]]);
    $this->actingAs(User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]));
    $this->get('/asset-labels')->assertInertia(fn (Assert $page) => $page
        ->component('AssetLabels/Index')->has('units', 1)
        ->where('units.0.code', 'LABEL-1')->where('units.0.purchase_year', 2026)
        ->where('units.0.funding_source', 'BOSP')->where('units.0.item', 'Barang 0'));
});
