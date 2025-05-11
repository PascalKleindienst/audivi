<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;

it('only admins can view the user module', function () {
    $user = User::factory(state: ['is_admin' => false])->create();

    actingAs($user)->get(route('admin.users.index'))
        ->assertForbidden();
    actingAs($user)->get(route('admin.users.edit', $user))
        ->assertForbidden();
    actingAs($user)->patch(route('admin.users.update', $user))
        ->assertForbidden();
    actingAs($user)->delete(route('admin.users.destroy', $user))
        ->assertForbidden();
});

it('renders the user index view with users data', function () {
    $users = User::factory()->count(3)->create();
    $admin = User::factory(state: ['is_admin' => true])->create();

    actingAs($admin)->get(route('admin.users.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('User/Index')
            ->has('users', 3)
        );
});

it('renders the edit view with user data', function () {
    $user = User::factory()->create();
    $admin = User::factory(state: ['is_admin' => true])->create();

    actingAs($admin)
        ->get(route('admin.users.edit', $user))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('User/Edit')
            ->has('user')
        );
});

it('updates the user and redirects', function () {
    $user = User::factory()->create();
    $data = ['name' => 'Updated Name', 'email' => $user->email, 'is_admin' => false];
    $admin = User::factory(state: ['is_admin' => true])->create();

    actingAs($admin)->patch(route('admin.users.update', $user), $data)
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('flash.banner', trans('user.updated'));

    expect($user->fresh()->name)->toBe('Updated Name')
        ->and($user->fresh()->email)->toBe($user->email);
});

it('deletes the user and redirects', function () {
    $user = User::factory()->create();
    $admin = User::factory(state: ['is_admin' => true])->create();

    actingAs($admin)->delete(route('admin.users.destroy', $user))
        ->assertRedirect(route('admin.users.index'))
        ->assertSessionHas('flash.banner', trans('user.deleted'));

    expect(User::where('id', $user->id)->exists())->toBeFalse();
});
