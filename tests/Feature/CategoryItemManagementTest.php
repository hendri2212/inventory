<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

function catalogFixture(): array
{
    $a = DB::table('institutions')->insertGetId(['code' => 'A', 'name' => 'Instansi A']);
    $b = DB::table('institutions')->insertGetId(['code' => 'B', 'name' => 'Instansi B']);
    $departments = $categories = $items = [];
    foreach ([$a, $a, $b] as $index => $institution) {
        $departments[] = DB::table('departments')->insertGetId(['institution_id' => $institution, 'code' => 'D'.$index, 'name' => 'Bidang '.$index]);
        $categories[] = DB::table('categories')->insertGetId(['institution_id' => $institution, 'name' => 'Kategori '.$index]);
        $items[] = DB::table('items')->insertGetId(['institution_id' => $institution, 'category_id' => $categories[$index], 'code' => 'I'.$index, 'name' => 'Barang '.$index]);
    }

    return [$a, $b, $departments, $categories, $items];
}

test('guests cannot access category item or photo endpoints', function () {
    foreach (['/categories', '/items', '/item-units/1/photo'] as $url) {
        $this->get($url)->assertRedirect('/login');
    }
});

test('superadmin can manage categories with unique names per institution', function () {
    [$a, $b, , , $items] = catalogFixture();
    $this->actingAs(User::factory()->superadmin()->create());
    $data = ['institution_id' => $a, 'name' => 'Elektronik', 'description' => 'Perangkat'];
    $this->post('/categories', $data)->assertSessionHasNoErrors();
    $id = DB::table('categories')->where('name', 'Elektronik')->value('id');
    $this->get('/categories?search=Elektronik&institution_id='.$a)->assertInertia(fn (Assert $page) => $page->component('Categories/Index')->has('categories.data', 1));
    $this->post('/categories', $data)->assertSessionHasErrors('name');
    $this->post('/categories', [...$data, 'institution_id' => $b])->assertSessionHasNoErrors();
    $this->put('/categories/'.$id, $data)->assertSessionHasNoErrors();
    $this->put('/categories/'.$id, [...$data, 'institution_id' => $b])->assertSessionHasErrors('institution_id');
    $used = DB::table('items')->where('id', $items[0])->value('category_id');
    $this->delete('/categories/'.$used)->assertSessionHasErrors('category');
    $this->delete('/categories/'.$id)->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('categories', ['id' => $id]);
});

test('category administration is isolated by institution and denied to field roles', function () {
    [$a, $b, $departments, $categories] = catalogFixture();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $a, 'department_id' => null]);
    $operator = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $this->actingAs($admin);
    $this->get('/categories')->assertInertia(fn (Assert $page) => $page->has('categories.data', 2)->has('institutions', 1));
    $this->post('/categories', ['institution_id' => $b, 'name' => 'Invalid'])->assertSessionHasErrors('institution_id');
    $this->put('/categories/'.$categories[2], ['institution_id' => $a, 'name' => 'Invalid'])->assertNotFound();
    $this->delete('/categories/'.$categories[2])->assertNotFound();
    $this->actingAs($operator);
    $this->get('/categories')->assertForbidden();
    $this->post('/categories', [])->assertForbidden();
    $this->delete('/categories/'.$categories[0])->assertForbidden();
});
