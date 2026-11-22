<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

function transferFixture(): array
{
    $a = DB::table('institutions')->insertGetId(['code' => 'SMK1', 'name' => 'Sekolah A']);
    $b = DB::table('institutions')->insertGetId(['code' => 'OTHER', 'name' => 'Sekolah B']);
    $departments = $rooms = [];
    foreach ([$a, $a, $b] as $index => $institution) {
        $departments[] = DB::table('departments')->insertGetId(['institution_id' => $institution, 'code' => 'D'.$index, 'name' => 'Bidang '.$index]);
        $rooms[] = DB::table('rooms')->insertGetId(['institution_id' => $institution, 'code' => 'R'.$index, 'name' => 'Ruang '.$index]);
    }
    $category = DB::table('categories')->insertGetId(['institution_id' => $a, 'name' => 'Elektronik']);
    $item = DB::table('items')->insertGetId(['institution_id' => $a, 'category_id' => $category, 'code' => 'SMK1-001', 'name' => 'Laptop']);
    $unit = DB::table('item_units')->insertGetId(['institution_id' => $a, 'item_id' => $item, 'department_id' => $departments[0], 'room_id' => $rooms[0], 'code' => 'RPL-001']);
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $a, 'department_id' => null]);
    $source = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $destination = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[1]]);
    $outsider = User::factory()->create(['role' => 'admin', 'institution_id' => $b, 'department_id' => null]);
    $payload = ['institution_id' => $a, 'type' => 'department', 'from_department_id' => $departments[0], 'to_department_id' => $departments[1], 'from_room_id' => $rooms[0], 'to_room_id' => $rooms[1], 'transferred_at' => now()->subMinute()->timezone('Asia/Makassar')->format('Y-m-d\TH:i'), 'notes' => 'Penataan laboratorium', 'unit_ids' => [$unit]];

    return compact('a', 'b', 'departments', 'rooms', 'unit', 'admin', 'source', 'destination', 'outsider', 'payload');
}

test('department transfers update placement preserve unit codes and keep readable history for both departments', function () {
    $f = transferFixture();
    $this->actingAs($f['admin']);
    $this->get('/item-transfers')->assertInertia(fn (Assert $p) => $p->component('ItemTransfers/Index')->where('units.0.item.name', 'Laptop')->where('can_create', true));
    $this->post('/item-transfers', $f['payload'])->assertSessionHasNoErrors();
    $id = DB::table('item_transfers')->value('id');
    $this->assertDatabaseHas('item_transfers', ['code' => 'SMK1-MUT-001', 'recorded_by' => $f['admin']->id, 'approved_by' => null]);
    $this->assertDatabaseHas('item_units', ['id' => $f['unit'], 'code' => 'RPL-001', 'department_id' => $f['departments'][1], 'room_id' => $f['rooms'][1]]);
    $this->assertDatabaseHas('item_transfer_items', ['item_transfer_id' => $id, 'item_unit_id' => $f['unit']]);
    foreach (['source', 'destination'] as $actor) {
        $this->actingAs($f[$actor]);
        $this->get('/item-transfers')->assertInertia(fn (Assert $p) => $p->has('transfers.data', 1)->where('can_create', false)->where('transfers.data.0.from_department_name', 'Bidang 0')->where('transfers.data.0.to_department_name', 'Bidang 1'));
        $this->get('/item-transfers/'.$id)->assertInertia(fn (Assert $p) => $p->has('units', 1)->where('units.0.name', 'Laptop'));
        $this->post('/item-transfers', $f['payload'])->assertForbidden();
    }
    $this->actingAs($f['outsider']);
    $this->get('/item-transfers')->assertInertia(fn (Assert $p) => $p->has('transfers.data', 0));
    $this->get('/item-transfers/'.$id)->assertNotFound();
});

test('room transfers allow unplacement and reject unchanged destinations and stale source', function () {
    $f = transferFixture();
    $this->actingAs($f['admin']);
    $data = [...$f['payload'], 'type' => 'room', 'to_department_id' => $f['departments'][0]];
    $this->post('/item-transfers', [...$data, 'to_room_id' => $f['rooms'][0]])->assertSessionHasErrors('to_room_id');
    $this->post('/item-transfers', [...$data, 'to_department_id' => $f['departments'][1]])->assertSessionHasErrors('to_department_id');
    $this->post('/item-transfers', [...$data, 'to_room_id' => null])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('item_units', ['id' => $f['unit'], 'department_id' => $f['departments'][0], 'room_id' => null]);
    $this->post('/item-transfers', $data)->assertSessionHasErrors('unit_ids');
    $this->post('/item-transfers', [...$data, 'from_room_id' => null])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('item_transfers', ['code' => 'SMK1-MUT-002']);
    $this->get('/?menu=mutasi')->assertRedirect('/item-transfers');
});

test('transfers reject foreign inactive duplicate future and borrowed units atomically', function () {
    $f = transferFixture();
    $this->get('/item-transfers')->assertRedirect('/login');
    $this->actingAs($f['admin']);
    $data = $f['payload'];
    $this->post('/item-transfers', [...$data, 'institution_id' => $f['b']])->assertNotFound();
    $this->post('/item-transfers', [...$data, 'to_department_id' => $f['departments'][2]])->assertSessionHasErrors('to_department_id');
    $this->post('/item-transfers', [...$data, 'to_room_id' => $f['rooms'][2]])->assertSessionHasErrors('to_room_id');
    $this->post('/item-transfers', [...$data, 'unit_ids' => [$f['unit'], $f['unit']]])->assertSessionHasErrors('unit_ids.0');
    $this->post('/item-transfers', [...$data, 'transferred_at' => now()->addHour()->timezone('Asia/Makassar')->format('Y-m-d\TH:i')])->assertSessionHasErrors('transferred_at');
    DB::table('rooms')->where('id', $f['rooms'][1])->update(['is_active' => false]);
    $this->post('/item-transfers', $data)->assertSessionHasErrors('to_room_id');
    DB::table('rooms')->where('id', $f['rooms'][1])->update(['is_active' => true]);
    $loan = DB::table('loans')->insertGetId(['institution_id' => $f['a'], 'department_id' => $f['departments'][0], 'code' => 'PJM-1', 'borrower_name' => 'Peminjam', 'borrowed_at' => now(), 'recorded_by' => $f['admin']->id]);
    DB::table('loan_items')->insert(['institution_id' => $f['a'], 'loan_id' => $loan, 'item_unit_id' => $f['unit'], 'active_item_unit_id' => $f['unit'], 'condition_out' => 'good']);
    $this->post('/item-transfers', $data)->assertSessionHasErrors('unit_ids');
    expect(DB::table('item_transfers')->count())->toBe(0);
    $this->assertDatabaseHas('item_units', ['id' => $f['unit'], 'department_id' => $f['departments'][0]]);
});
