<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('guests and field roles cannot manage departments', function () {
    $this->get('/departments')->assertRedirect('/login');
    foreach (['operator'] as $role) {
        $this->app['auth']->forgetGuards();
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        $this->get('/departments')->assertForbidden();
        $this->post('/departments', [])->assertForbidden();
        $this->put('/departments/'.$user->department_id, [])->assertForbidden();
        $this->delete('/departments/'.$user->department_id)->assertForbidden();
    }
});

test('superadmin can create search filter update and delete departments', function () {
    $institution = DB::table('institutions')->insertGetId(['code' => 'TEST', 'name' => 'Instansi Uji']);
    $this->actingAs(User::factory()->superadmin()->create());
    $data = ['institution_id' => $institution, 'code' => 'IT', 'name' => 'Teknologi', 'description' => 'Unit IT', 'is_active' => true];
    $this->post('/departments', $data)->assertSessionHasNoErrors()->assertRedirect('/departments');
    $id = DB::table('departments')->where('code', 'IT')->value('id');
    $this->get('/departments?search=Tekno&institution_id='.$institution)->assertInertia(fn (Assert $page) => $page
        ->component('Departments/Index')->has('departments.data', 1)->where('departments.data.0.code', 'IT'));
    $this->get('/departments?search=missing')->assertInertia(fn (Assert $page) => $page->has('departments.data', 0));
    $this->put('/departments/'.$id, [...$data, 'name' => 'Diubah', 'is_active' => false])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('departments', ['id' => $id, 'name' => 'Diubah', 'is_active' => false]);
    $this->delete('/departments/'.$id)->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('departments', ['id' => $id]);
});

test('admin department management is isolated to own institution', function () {
    $member = User::factory()->create();
    $other = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $member->institution_id, 'department_id' => null]);
    $this->actingAs($admin);
    $this->get('/departments')->assertInertia(fn (Assert $page) => $page->has('departments.data', 1)->has('institutions', 1));
    $this->get('/departments?institution_id='.$other->institution_id)->assertInertia(fn (Assert $page) => $page->has('departments.data', 0));
    $data = ['institution_id' => $member->institution_id, 'code' => 'NEW', 'name' => 'Bidang Baru', 'is_active' => true];
    $this->post('/departments', $data)->assertSessionHasNoErrors();
    $id = DB::table('departments')->where('code', 'NEW')->value('id');
    $this->put('/departments/'.$id, [...$data, 'name' => 'Updated'])->assertSessionHasNoErrors();
    $this->post('/departments', [...$data, 'institution_id' => $other->institution_id])->assertSessionHasErrors('institution_id');
    $this->put('/departments/'.$other->department_id, $data)->assertNotFound();
    $this->delete('/departments/'.$other->department_id)->assertNotFound();
    $this->delete('/departments/'.$id)->assertSessionHasNoErrors();
});

test('department codes are unique per institution and assignments cannot be moved', function () {
    $a = DB::table('institutions')->insertGetId(['code' => 'A', 'name' => 'Instansi A']);
    $b = DB::table('institutions')->insertGetId(['code' => 'B', 'name' => 'Instansi B']);
    $this->actingAs(User::factory()->superadmin()->create());
    $data = ['institution_id' => $a, 'code' => 'IT', 'name' => 'Teknologi', 'is_active' => true];
    $this->post('/departments', $data)->assertSessionHasNoErrors();
    $this->post('/departments', $data)->assertSessionHasErrors('code');
    $this->post('/departments', [...$data, 'institution_id' => $b])->assertSessionHasNoErrors();
    $id = DB::table('departments')->where('institution_id', $a)->value('id');
    $this->put('/departments/'.$id, $data)->assertSessionHasNoErrors();
    $this->put('/departments/'.$id, [...$data, 'institution_id' => $b, 'code' => 'NEW'])->assertSessionHasErrors('institution_id');
    $this->post('/departments', [...$data, 'institution_id' => 99999, 'code' => '', 'name' => '', 'is_active' => 'invalid'])->assertSessionHasErrors(['institution_id', 'code', 'name', 'is_active']);
});

test('departments with users or items cannot be deleted', function (string $relation) {
    $member = User::factory()->create();
    $id = $member->department_id;
    if ($relation === 'items') {
        $category = DB::table('categories')->insertGetId(['institution_id' => $member->institution_id, 'name' => 'Kategori Uji']);
        $item = DB::table('items')->insertGetId(['category_id' => $category, 'institution_id' => $member->institution_id, 'code' => 'ITEM', 'name' => 'Barang Uji']);
        DB::table('item_units')->insert(['item_id' => $item, 'institution_id' => $member->institution_id, 'department_id' => $id, 'code' => 'UNIT']);
        $member->delete();
    }
    $this->actingAs(User::factory()->superadmin()->create());
    $this->delete('/departments/'.$id)->assertSessionHasErrors('department');
    $this->assertDatabaseHas('departments', ['id' => $id]);
})->with(['users', 'items']);
