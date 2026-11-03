<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('guests cannot manage institutions', function () {
    $this->get('/institutions')->assertRedirect('/login');
    $this->post('/institutions', [])->assertRedirect('/login');
});

test('only superadmins can access institution management endpoints', function (string $role) {
    $user = User::factory()->create(['role' => $role, ...($role === 'admin' ? ['department_id' => null] : [])]);
    $this->actingAs($user);
    $this->get('/institutions')->assertForbidden();
    $this->post('/institutions', ['code' => 'NEW', 'name' => 'New', 'is_active' => true])->assertForbidden();
    $this->put('/institutions/'.$user->institution_id, ['name' => 'Changed'])->assertForbidden();
    $this->delete('/institutions/'.$user->institution_id)->assertForbidden();
})->with(['admin', 'operator']);

test('superadmins can list search create update and delete institutions', function () {
    $this->actingAs(User::factory()->superadmin()->create());
    $this->post('/institutions', ['code' => 'TEST', 'name' => 'Instansi Uji', 'is_active' => true])->assertRedirect('/institutions');
    $id = DB::table('institutions')->where('code', 'TEST')->value('id');
    $this->get('/institutions?search=Uji')->assertInertia(fn (Assert $page) => $page
        ->component('Institutions/Index')->has('institutions.data', 1)->where('institutions.data.0.code', 'TEST'));
    $this->get('/institutions?search=missing')->assertInertia(fn (Assert $page) => $page->has('institutions.data', 0));
    $this->put('/institutions/'.$id, ['code' => 'TEST', 'name' => 'Diubah', 'is_active' => false])->assertSessionHasNoErrors();
    $this->assertDatabaseHas('institutions', ['id' => $id, 'name' => 'Diubah', 'is_active' => false]);
    $this->delete('/institutions/'.$id)->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('institutions', ['id' => $id]);
});

test('institution validation rejects duplicate codes and invalid fields', function () {
    $this->actingAs(User::factory()->superadmin()->create());
    DB::table('institutions')->insert(['code' => 'DUP', 'name' => 'Existing']);
    $this->post('/institutions', ['code' => 'DUP', 'name' => '', 'email' => 'invalid', 'phone' => str_repeat('1', 31), 'is_active' => 'invalid'])
        ->assertSessionHasErrors(['code', 'name', 'email', 'phone', 'is_active']);
});

test('institutions with related data cannot be deleted', function () {
    $member = User::factory()->create();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->delete('/institutions/'.$member->institution_id)->assertSessionHasErrors('institution');
    $this->assertDatabaseHas('institutions', ['id' => $member->institution_id]);
});
