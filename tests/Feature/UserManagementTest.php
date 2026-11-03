<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;

function userManagementPayload(User $user): array
{
    return ['name' => 'Pengguna Baru', 'email' => 'new@example.com', 'role' => 'operator',
        'institution_id' => $user->institution_id, 'department_id' => $user->department_id,
        'password' => 'password123', 'password_confirmation' => 'password123'];
}

test('guests and field roles cannot manage users', function () {
    $this->get('/users')->assertRedirect('/login');
    $user = User::factory()->create();
    foreach (['operator'] as $role) {
        DB::table('users')->where('id', $user->id)->update(['role' => $role]);
        $this->actingAs($user->fresh());
        $this->get('/users')->assertForbidden();
        $this->post('/users', userManagementPayload($user))->assertForbidden();
        $this->put('/users/'.$user->id, userManagementPayload($user))->assertForbidden();
        $this->delete('/users/'.$user->id)->assertForbidden();
    }
});

test('superadmin can manage users and preserve an unchanged password', function () {
    $member = User::factory()->create();
    $this->actingAs(User::factory()->superadmin()->create());
    $data = userManagementPayload($member);
    $this->post('/users', $data)->assertSessionHasNoErrors()->assertRedirect('/users');
    $target = User::where('email', $data['email'])->firstOrFail();
    expect(Hash::check('password123', $target->password))->toBeTrue();
    $this->get('/users?search=new@example.com')->assertInertia(fn (Assert $page) => $page
        ->component('Users/Index')->has('users.data', 1)->missing('users.data.0.password'));
    $this->put('/users/'.$target->id, [...$data, 'name' => 'Diubah', 'password' => '', 'password_confirmation' => ''])
        ->assertSessionHasNoErrors();
    expect($target->fresh()->name)->toBe('Diubah')->and($target->fresh()->password)->toBe($target->password);
    $this->put('/users/'.$target->id, [...$data, 'password' => 'changed123', 'password_confirmation' => 'changed123'])->assertSessionHasNoErrors();
    expect(Hash::check('changed123', $target->fresh()->password))->toBeTrue();
    $this->delete('/users/'.$target->id)->assertSessionHasNoErrors();
    $this->assertDatabaseMissing('users', ['id' => $target->id]);
});

test('admin can manage only field accounts in own institution', function () {
    $member = User::factory()->create();
    $other = User::factory()->create();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $member->institution_id, 'department_id' => null]);
    $this->actingAs($admin);
    $data = userManagementPayload($member);
    $this->post('/users', $data)->assertSessionHasNoErrors();
    $this->put('/users/'.$member->id, [...$data, 'email' => $member->email])->assertSessionHasNoErrors();
    $this->put('/users/'.$other->id, $data)->assertForbidden();
    $this->delete('/users/'.$other->id)->assertForbidden();
    $this->put('/users/'.$admin->id, $data)->assertForbidden();
    $this->post('/users', [...$data, 'email' => 'promote@example.com', 'role' => 'superadmin', 'institution_id' => null, 'department_id' => null])->assertSessionHasErrors('role');
    $this->post('/users', [...$data, 'institution_id' => $other->institution_id, 'department_id' => $other->department_id])->assertSessionHasErrors('institution_id');
    $this->get('/users')->assertInertia(fn (Assert $page) => $page->has('users.data', 3)->has('institutions', 1)->has('departments', 1));
    $this->delete('/users/'.$member->id)->assertSessionHasNoErrors();
});

test('user validation prevents duplicate emails invalid assignments and self deletion', function () {
    $member = User::factory()->create();
    $other = User::factory()->create();
    $superadmin = User::factory()->superadmin()->create();
    $this->actingAs($superadmin);
    $data = userManagementPayload($member);
    $this->post('/users', [...$data, 'email' => $member->email, 'password_confirmation' => 'different', 'department_id' => $other->department_id])->assertSessionHasErrors(['email', 'password', 'department_id']);
    $this->post('/users', [...$data, 'role' => 'admin'])->assertSessionHasErrors('department_id');
    $this->delete('/users/'.$superadmin->id)->assertForbidden();
    DB::table('institutions')->where('id', $member->institution_id)->update(['is_active' => false]);
    $this->post('/users', $data)->assertSessionHasErrors('institution_id');
});

test('superadmin can change role and clear obsolete assignments', function () {
    $member = User::factory()->create();
    $this->actingAs(User::factory()->superadmin()->create());
    $this->put('/users/'.$member->id, ['name' => $member->name, 'email' => $member->email, 'role' => 'superadmin'])->assertSessionHasNoErrors();
    expect($member->fresh()->role)->toBe('superadmin')->and($member->fresh()->institution_id)->toBeNull()->and($member->fresh()->department_id)->toBeNull();
});
