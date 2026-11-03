<?php

use App\Models\ItemUnit;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

function unitFixture(): array
{
    [$a, $b, $departments, $categories, $items] = catalogFixture();
    $rooms = [];
    foreach ([$a, $b] as $i => $institution) {
        $rooms[] = DB::table('rooms')->insertGetId(['institution_id' => $institution, 'code' => 'R'.$i, 'name' => 'Ruang '.$i]);
    }

    return [$a, $b, $departments, $categories, $items, $rooms];
}

function unitPayload(array $fixture): array
{
    [$a, , $departments, , $items, $rooms] = $fixture;

    return ['institution_id' => $a, 'item_id' => $items[0], 'department_id' => $departments[0], 'room_id' => $rooms[0],
        'code' => 'UNIT-001', 'serial_number' => 'SN-1', 'purchase_year' => 2026, 'purchase_price' => '12500000.50',
        'funding_source' => 'BOSP', 'condition' => 'good', 'is_active' => true];
}

test('catalog metadata is shared within institution and unit counts respect department visibility', function () {
    $fixture = unitFixture();
    [$a, , $departments, , $items] = $fixture;
    $payload = unitPayload($fixture);
    DB::table('item_units')->insert($payload);
    DB::table('item_units')->insert([...$payload, 'code' => 'UNIT-002', 'department_id' => $departments[1]]);
    $this->actingAs(User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]));
    $this->get('/items')->assertInertia(fn (Assert $page) => $page->has('items.data', 2)->where('can_create', false)->where('items.data.0.units_count', 1));
    $this->post('/items', [])->assertForbidden();
    $this->put('/items/'.$items[0], [])->assertForbidden();
    $this->get('/item-units')->assertInertia(fn (Assert $page) => $page->has('units.data', 1));
});

test('admins manage catalog without storing unit fields and cannot delete a catalog in use', function () {
    [$a, , , $categories] = $fixture = unitFixture();
    $this->actingAs(User::factory()->superadmin()->create());
    $data = ['institution_id' => $a, 'category_id' => $categories[0], 'code' => 'NEW', 'name' => 'Laptop', 'unit' => 'unit', 'is_active' => true, 'quantity' => 100];
    $this->post('/items', $data)->assertSessionHasNoErrors();
    $item = DB::table('items')->where('code', 'NEW')->first();
    expect(property_exists($item, 'quantity'))->toBeFalse();
    $this->put('/items/'.$item->id, [...$data, 'name' => 'Laptop Baru'])->assertSessionHasNoErrors();
    DB::table('item_units')->insert([...unitPayload($fixture), 'item_id' => $item->id]);
    $this->delete('/items/'.$item->id)->assertSessionHasErrors('item');
});

test('operators create edit inspect and delete individual units with private photos', function () {
    Storage::fake('local');
    [$a, , $departments] = $fixture = unitFixture();
    $operator = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $this->actingAs($operator);
    $data = unitPayload($fixture);
    $photo = UploadedFile::fake()->createWithContent('photo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
    $this->post('/item-units', [...$data, 'photo' => $photo])->assertSessionHasNoErrors();
    $unit = ItemUnit::firstOrFail();
    Storage::disk('local')->assertExists($unit->getAttribute('photo_path'));
    $this->get('/item-units/'.$unit->getKey().'/photo')->assertOk();
    $this->get('/item-units?search=UNIT-001')->assertInertia(fn (Assert $page) => $page->component('ItemUnits/Index')->has('units.data', 1)->where('units.data.0.purchase_price', '12500000.50'));
    $this->put('/item-units/'.$unit->getKey(), [...$data, 'condition' => 'minor_damage', 'remove_photo' => true])->assertSessionHasNoErrors();
    Storage::disk('local')->assertMissing($unit->getAttribute('photo_path'));
    $this->get('/item-units/'.$unit->getKey().'/photo')->assertNotFound();
    $this->delete('/item-units/'.$unit->getKey())->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('item_units', ['id' => $unit->getKey()]);
});

test('unit requests reject cross institution references forged departments duplicate codes and invalid attributes', function () {
    [$a, $b, $departments, , $items, $rooms] = $fixture = unitFixture();
    $this->actingAs(User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]));
    $data = unitPayload($fixture);
    $this->post('/item-units', [...$data, 'institution_id' => $b])->assertSessionHasErrors('institution_id');
    $this->post('/item-units', [...$data, 'item_id' => $items[2], 'department_id' => $departments[1], 'room_id' => $rooms[1], 'purchase_price' => -1, 'condition' => 'invalid'])->assertSessionHasErrors(['item_id', 'department_id', 'room_id', 'purchase_price', 'condition']);
    $this->post('/item-units', $data)->assertSessionHasNoErrors();
    $this->post('/item-units', $data)->assertSessionHasErrors('code');
    $id = DB::table('item_units')->value('id');
    $this->put('/item-units/'.$id, [...$data, 'room_id' => null])->assertSessionHasErrors('room_id');
    $this->app['auth']->forgetGuards();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->put('/item-units/'.$id, [...$data, 'department_id' => $departments[1]])->assertSessionHasErrors('department_id');
});

test('unit routes hide other department assets and private photos', function () {
    [$a, , $departments] = $fixture = unitFixture();
    $id = DB::table('item_units')->insertGetId([...unitPayload($fixture), 'department_id' => $departments[1]]);
    $this->actingAs(User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]));
    $this->get('/item-units/'.$id.'/photo')->assertNotFound();
    $this->put('/item-units/'.$id, unitPayload($fixture))->assertNotFound();
    $this->delete('/item-units/'.$id)->assertNotFound();
});

test('rooms are institution scoped administered by admins and readable by operators', function () {
    [$a, , $departments, , , $rooms] = $fixture = unitFixture();
    $this->actingAs(User::factory()->create(['role' => 'admin', 'institution_id' => $a, 'department_id' => null]));
    $data = ['institution_id' => $a, 'code' => 'NEW', 'name' => 'Ruang Baru', 'is_active' => true];
    $this->post('/rooms', $data)->assertSessionHasNoErrors();
    $id = DB::table('rooms')->where('code', 'NEW')->value('id');
    $this->put('/rooms/'.$id, [...$data, 'name' => 'Ruang Edit'])->assertSessionHasNoErrors();
    $this->post('/rooms', $data)->assertSessionHasErrors('code');
    $this->put('/rooms/'.$rooms[1], $data)->assertNotFound();
    DB::table('item_units')->insert(unitPayload($fixture));
    $this->delete('/rooms/'.$rooms[0])->assertSessionHasErrors('room');
    $this->delete('/rooms/'.$id)->assertSessionHasNoErrors();
    $this->app['auth']->forgetGuards();
    $operator = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $this->actingAs($operator);
    $this->get('/rooms')->assertInertia(fn (Assert $page) => $page->component('Rooms/Index')->has('rooms.data', 1)->where('can_create', false));
    $this->post('/rooms', $data)->assertForbidden();
});

test('removed borrower role is rejected by user validation', function () {
    $member = User::factory()->create();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->post('/users', [...userManagementPayload($member), 'role' => 'peminjam'])->assertSessionHasErrors('role');
});

test('unit foreign keys reject references outside their institution', function (string $field) {
    [$a, $b, $departments, , $items, $rooms] = $fixture = unitFixture();
    $other = ['item_id' => $items[2], 'department_id' => $departments[2], 'room_id' => $rooms[1]];
    expect(fn () => DB::table('item_units')->insert([...unitPayload($fixture), $field => $other[$field]]))->toThrow(QueryException::class);
})->with(['item_id', 'department_id', 'room_id']);

test('loan details support partial returns and database prevents two active claims for the same unit', function () {
    [$a, , $departments] = $fixture = unitFixture();
    $actor = User::factory()->superadmin()->create();
    $unit1 = DB::table('item_units')->insertGetId(unitPayload($fixture));
    $unit2 = DB::table('item_units')->insertGetId([...unitPayload($fixture), 'code' => 'UNIT-002']);
    $loan = DB::table('loans')->insertGetId(['institution_id' => $a, 'department_id' => $departments[0], 'code' => 'LOAN-1', 'borrower_name' => 'Pegawai Tanpa Akun', 'borrowed_at' => now(), 'recorded_by' => $actor->id]);
    $detail = ['institution_id' => $a, 'loan_id' => $loan, 'item_unit_id' => $unit1, 'active_item_unit_id' => $unit1, 'condition_out' => 'good'];
    $first = DB::table('loan_items')->insertGetId($detail);
    DB::table('loan_items')->insert([...$detail, 'item_unit_id' => $unit2, 'active_item_unit_id' => $unit2]);
    $otherLoan = DB::table('loans')->insertGetId(['institution_id' => $a, 'department_id' => $departments[0], 'code' => 'LOAN-2', 'borrower_name' => 'Orang Lain', 'borrowed_at' => now(), 'recorded_by' => $actor->id]);
    expect(fn () => DB::table('loan_items')->insert([...$detail, 'loan_id' => $otherLoan]))->toThrow(QueryException::class);
    DB::table('loan_items')->where('id', $first)->update(['active_item_unit_id' => null, 'returned_at' => now(), 'condition_in' => 'minor_damage', 'returned_by' => $actor->id]);
    expect(DB::table('loan_items')->where('loan_id', $loan)->whereNull('returned_at')->count())->toBe(1);
    DB::table('loan_items')->insert([...$detail, 'loan_id' => $otherLoan]);
    $this->actingAs($actor);
    $this->delete('/item-units/'.$unit1)->assertSessionHasErrors('unit');
    $this->put('/item-units/'.$unit1, unitPayload($fixture))->assertSessionHasErrors('unit');
});

test('transaction details reject units from other institutions', function (string $type) {
    [$a, $b, $departments, , $items] = $fixture = unitFixture();
    $actor = User::factory()->superadmin()->create();
    $otherUnit = DB::table('item_units')->insertGetId(['institution_id' => $b, 'item_id' => $items[2], 'department_id' => $departments[2], 'code' => 'OTHER']);
    if ($type === 'loan') {
        $loan = DB::table('loans')->insertGetId(['institution_id' => $a, 'department_id' => $departments[0], 'code' => 'L-1', 'borrower_name' => 'Peminjam', 'borrowed_at' => now(), 'recorded_by' => $actor->id]);
        expect(fn () => DB::table('loan_items')->insert(['institution_id' => $a, 'loan_id' => $loan, 'item_unit_id' => $otherUnit, 'condition_out' => 'good']))->toThrow(QueryException::class);
    } else {
        $transfer = DB::table('item_transfers')->insertGetId(['institution_id' => $a, 'code' => 'T-1', 'type' => 'department', 'from_department_id' => $departments[0], 'to_department_id' => $departments[1], 'transferred_at' => now(), 'recorded_by' => $actor->id]);
        expect(fn () => DB::table('item_transfer_items')->insert(['institution_id' => $a, 'item_transfer_id' => $transfer, 'item_unit_id' => $otherUnit]))->toThrow(QueryException::class);
    }
})->with(['loan', 'transfer']);
