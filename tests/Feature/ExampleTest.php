<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guests must log in before opening home', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});

test('login page is available to guests', function () {
    $this->get(route('login'))->assertOk();
});

test('users can log in and open home', function () {
    $user = User::factory()->create();
    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
        'remember' => true,
    ])->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);
    $this->get(route('home'))->assertOk();
    $this->get(route('login'))->assertRedirect(route('home'));
});

test('incorrect credentials do not authenticate', function () {
    $user = User::factory()->create();
    $this->from(route('login'))->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'incorrect',
    ])->assertRedirect(route('login'))->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('users can log out and lose access to home', function () {
    $user = User::factory()->create();
    $this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();
    $this->get(route('home'))->assertRedirect(route('login'));
});

test('login attempts are rate limited', function () {
    for ($attempt = 0; $attempt < 5; $attempt++) {
        $this->post(route('login.store'), ['email' => 'unknown@example.com', 'password' => 'incorrect']);
    }
    $this->post(route('login.store'), ['email' => 'unknown@example.com', 'password' => 'incorrect'])->assertTooManyRequests();
});
