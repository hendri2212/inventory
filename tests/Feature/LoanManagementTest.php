<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

function lendingFixture(): array
{
    $a = DB::table('institutions')->insertGetId(['code' => 'A', 'name' => 'Sekolah A']);
    $b = DB::table('institutions')->insertGetId(['code' => 'B', 'name' => 'Sekolah B']);
    $departments = [];
    foreach ([$a, $a, $b] as $index => $institution) {
        $departments[] = DB::table('departments')->insertGetId(['institution_id' => $institution, 'code' => 'D'.$index, 'name' => 'Bidang '.$index]);
    }
    $category = DB::table('categories')->insertGetId(['institution_id' => $a, 'name' => 'Komputer']);
    $item = DB::table('items')->insertGetId(['institution_id' => $a, 'category_id' => $category, 'code' => 'A-001', 'name' => 'Laptop']);
    $fixture = [$a, $b, $departments];
    $unit = ['institution_id' => $a, 'department_id' => $departments[0], 'item_id' => $item, 'code' => 'FIRST', 'condition' => 'good'];
    [$a, , $departments] = $fixture;
    $first = DB::table('item_units')->insertGetId($unit);
    $second = DB::table('item_units')->insertGetId([...$unit, 'code' => 'SECOND']);
    $actor = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $data = ['department_id' => $departments[0], 'borrower_name' => 'Siswa XI', 'borrowed_at' => now()->subMinute()->timezone('Asia/Makassar')->format('Y-m-d\TH:i'), 'due_at' => now()->addDay()->timezone('Asia/Makassar')->format('Y-m-d\TH:i'), 'notes' => 'Praktik kelas', 'items' => [['item_unit_id' => $first, 'condition_out' => 'good', 'notes_out' => 'Charger'], ['item_unit_id' => $second, 'condition_out' => 'minor_damage']]];

    return [$fixture, $actor, $data, $first, $second];
}

test('lending supports partial returns and prevents double lending or repeated returns', function () {
    [$fixture, $actor, $data, $first, $second] = lendingFixture();
    $this->actingAs($actor);
    $this->post('/loans', $data)->assertSessionHasNoErrors();
    $loan = DB::table('loans')->first();
    expect($loan->code)->toBe('D0-PJM-001');
    $this->get('/?menu=peminjaman')->assertRedirect('/loans');
    $this->get('/loans/'.$loan->id)->assertInertia(fn (Assert $p) => $p->component('Loans/Show')->has('loan.items', 2));
    $this->post('/loans', $data)->assertSessionHasErrors('items');
    $entry = DB::table('loan_items')->where('item_unit_id', $first)->first();
    $returns = ['items' => [['id' => $entry->id, 'condition_in' => 'minor_damage', 'notes_in' => 'Goresan']]];
    $this->post('/loans/'.$loan->id.'/returns', $returns)->assertSessionHasNoErrors();
    $this->assertDatabaseHas('item_units', ['id' => $first, 'condition' => 'minor_damage', 'condition_notes' => 'Goresan']);
    $this->assertDatabaseHas('loan_items', ['id' => $entry->id, 'active_item_unit_id' => null, 'returned_by' => $actor->id]);
    $this->get('/loans?status=partial')->assertInertia(fn (Assert $p) => $p->has('loans.data', 1)->where('loans.data.0.returned_count', 1));
    $this->post('/loans/'.$loan->id.'/returns', $returns)->assertSessionHasErrors('items');
    $other = DB::table('loan_items')->where('item_unit_id', $second)->value('id');
    $this->post('/loans/'.$loan->id.'/returns', ['items' => [['id' => $other, 'condition_in' => 'good']]])->assertSessionHasNoErrors();
    $this->get('/loans?status=completed')->assertInertia(fn (Assert $p) => $p->has('loans.data', 1)->where('loans.data.0.returned_count', 2));
    $this->post('/loans', $data)->assertSessionHasNoErrors();
    $this->assertDatabaseHas('loans', ['code' => 'D0-PJM-002']);
});

test('lending rejects foreign departments unavailable units duplicates and future dates', function () {
    [$fixture, $actor, $data, $first] = lendingFixture();
    $this->actingAs($actor);
    $this->post('/loans', [...$data, 'department_id' => $fixture[2][1]])->assertNotFound();
    $this->post('/loans', [...$data, 'items' => [$data['items'][0], $data['items'][0]]])->assertSessionHasErrors('items.0.item_unit_id');
    DB::table('item_units')->where('id', $first)->update(['condition' => 'major_damage']);
    $this->post('/loans', $data)->assertSessionHasErrors('items');
    $this->post('/loans', [...$data, 'borrowed_at' => now()->addHour()->timezone('Asia/Makassar')->format('Y-m-d\TH:i')])->assertSessionHasErrors('borrowed_at');
    expect(DB::table('loans')->count())->toBe(0);
});

test('operators cannot view or return loans belonging to another field', function () {
    [$fixture, $actor, $data] = lendingFixture();
    $this->get('/loans')->assertRedirect('/login');
    $this->actingAs($actor)->post('/loans', $data)->assertSessionHasNoErrors();
    $loan = DB::table('loans')->value('id');
    $this->app['auth']->forgetGuards();
    $other = User::factory()->create(['role' => 'operator', 'institution_id' => $fixture[0], 'department_id' => $fixture[2][1]]);
    $this->actingAs($other);
    $this->get('/loans')->assertInertia(fn (Assert $p) => $p->has('loans.data', 0)->has('units', 0));
    $this->get('/loans/'.$loan)->assertNotFound();
    $this->post('/loans/'.$loan.'/returns', ['items' => []])->assertNotFound();
});

test('loan input rejects forged transaction fields and return batches roll back on invalid units', function () {
    [, $actor, $data] = lendingFixture();
    $this->actingAs($actor);
    $forged = $data;
    $forged['items'][0]['returned_at'] = now();
    $this->post('/loans', $forged)->assertSessionHasErrors('items.0');
    $this->post('/loans', $data)->assertSessionHasNoErrors();
    $loan = DB::table('loans')->value('id');
    $entry = DB::table('loan_items')->value('id');
    $this->post('/loans/'.$loan.'/returns', ['items' => [['id' => $entry, 'condition_in' => 'good'], ['id' => 99999, 'condition_in' => 'good']]])->assertSessionHasErrors('items');
    $this->assertDatabaseHas('loan_items', ['id' => $entry, 'returned_at' => null]);
});

test('admin can lend assets across own departments but cannot access another institution', function () {
    [$fixture, , $data] = lendingFixture();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $fixture[0], 'department_id' => null]);
    $this->actingAs($admin);
    $this->post('/loans', $data)->assertSessionHasNoErrors();
    $this->post('/loans', [...$data, 'department_id' => $fixture[2][2]])->assertNotFound();
    $this->get('/loans')->assertInertia(fn (Assert $p) => $p->has('loans.data', 1)->has('departments', 2));
});

test('available lending units include their catalog name for the department selector', function () {
    [, $actor, , $first] = lendingFixture();
    $this->actingAs($actor);
    $this->get('/loans')->assertInertia(fn (Assert $page) => $page
        ->component('Loans/Index')
        ->has('units', 2)
        ->where('units.0.id', $first)
        ->where('units.0.item.name', 'Laptop')
        ->where('units.1.item.name', 'Laptop'));
});
