<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

function reportFixture(): array
{
    [$a, $b, $departments, $categories, $items, $rooms] = $fixture = unitFixture();
    $base = unitPayload($fixture);
    DB::table('item_units')->insert($base);
    DB::table('item_units')->insert([...$base, 'code' => 'UNIT-002', 'department_id' => $departments[1], 'condition' => 'minor_damage']);
    DB::table('item_units')->insert([...$base, 'code' => 'UNIT-003', 'condition' => null]);
    DB::table('item_units')->insert([...$base, 'institution_id' => $b, 'item_id' => $items[2], 'department_id' => $departments[2], 'room_id' => $rooms[1], 'code' => 'PRIVATE']);

    return $fixture;
}

test('reports require authentication and valid tenant assignment', function () {
    $this->get('/reports')->assertRedirect('/login');
    $this->get('/reports?print=1')->assertRedirect('/login');
    $user = User::factory()->create();
    DB::table('departments')->where('id', $user->department_id)->update(['is_active' => false]);
    $this->actingAs($user);
    $this->get('/reports')->assertForbidden();
});

test('report groups catalog with complete unit details and excludes empty catalogs', function () {
    reportFixture();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->get('/reports')->assertOk()->assertInertia(fn (Assert $page) => $page->component('Reports/Index')
        ->has('items', 2)->where('items.0.units_count', 3)->has('items.0.units', 3)
        ->where('items.0.units.0.serial_number', 'SN-1')->where('items.0.units.0.purchase_price', '12500000.50')
        ->where('items.0.units.0.department', 'Bidang 0')->where('items.0.units.0.room', 'Ruang 0')
        ->where('items.0.units.0.funding_source', 'BOSP')->missing('items.0.units.0.photo_path')
        ->where('pagination.total', 2)->where('printing', false));
});

test('report and print never expose units outside the actor scope', function (string $role, int $units) {
    [$a, , $departments] = reportFixture();
    $this->actingAs(User::factory()->create(['role' => $role, 'institution_id' => $a, 'department_id' => $role === 'operator' ? $departments[0] : null]));
    foreach (['/reports', '/reports?print=1'] as $url) {
        $this->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page->has('items', 1)->where('items.0.units_count', $units)
            ->has('items.0.units', $units)->has('institutions', 1));
    }
    $this->get('/reports?institution_id=2&print=1')->assertInertia(fn (Assert $page) => $page->has('items', 0));
})->with([['admin', 3], ['operator', 2]]);

test('unit filters apply to both matching catalogs and their nested unit details', function (string $filter, string $code) {
    reportFixture();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->get('/reports?institution_id=1&'.$filter)->assertInertia(fn (Assert $page) => $page->has('items', 1)->has('items.0.units', 1)
        ->where('items.0.units_count', 1)->where('items.0.units.0.code', $code));
})->with([
    ['condition=minor_damage', 'UNIT-002'],
    ['condition=unknown', 'UNIT-003'],
    ['department_id=2&room_id=1', 'UNIT-002'],
]);

test('catalog search category and institution filters compose correctly', function () {
    [$a, , , $categories] = reportFixture();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->get('/reports?search=Barang%200&institution_id='.$a.'&category_id='.$categories[0])
        ->assertInertia(fn (Assert $page) => $page->has('items', 1)->has('items.0.units', 3));
    $this->get('/reports?search=TidakAda')->assertInertia(fn (Assert $page) => $page->has('items', 0)->where('pagination.total', 0));
    $this->get('/reports?condition=invalid&room_id=-1')->assertSessionHasErrors(['condition', 'room_id']);
    $this->get('/?menu=laporan')->assertRedirect('/reports');
});

test('print contains every matching item while the screen paginates catalogs', function () {
    [$a, , $departments, $categories] = $fixture = unitFixture();
    for ($i = 1; $i <= 12; $i++) {
        $item = DB::table('items')->insertGetId(['institution_id' => $a, 'category_id' => $categories[0], 'code' => 'CAT'.$i, 'name' => 'Barang '.$i]);
        DB::table('item_units')->insert([...unitPayload($fixture), 'item_id' => $item, 'department_id' => $departments[0], 'code' => 'U'.$i]);
    }
    $this->actingAs(User::factory()->superadmin()->create());
    $this->get('/reports')->assertInertia(fn (Assert $page) => $page->has('items', 10)->where('pagination.total', 12));
    $this->get('/reports?print=1')->assertInertia(fn (Assert $page) => $page->has('items', 12)->where('pagination', null)->where('printing', true));
});
