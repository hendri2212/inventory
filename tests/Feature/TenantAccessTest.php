<?php

use App\Models\Category;
use App\Models\Department;
use App\Models\Institution;
use App\Models\Item;
use App\Models\ItemUnit;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

function tenantAccessFixture(): array
{
    $a = DB::table('institutions')->insertGetId(['code' => 'A', 'name' => 'Sekolah A']);
    $b = DB::table('institutions')->insertGetId(['code' => 'B', 'name' => 'Sekolah B']);
    $departments = [];
    $categories = [];
    $items = [];
    foreach ([$a, $a, $b] as $index => $institution) {
        $departments[] = DB::table('departments')->insertGetId([
            'institution_id' => $institution, 'code' => 'D'.$index, 'name' => 'Bidang '.$index,
        ]);
        $categories[] = DB::table('categories')->insertGetId([
            'institution_id' => $institution, 'name' => 'Kategori '.$index,
        ]);
        $items[] = DB::table('items')->insertGetId([
            'institution_id' => $institution,
            'category_id' => $categories[$index], 'code' => 'I'.$index, 'name' => 'Barang '.$index,
        ]);
        $items[$index] = DB::table('item_units')->insertGetId([
            'institution_id' => $institution, 'item_id' => $items[$index],
            'department_id' => $departments[$index], 'code' => 'UNIT'.$index,
        ]);
    }

    return [$a, $b, $departments, $categories, $items];
}

test('inventory queries are restricted by role institution and department', function (string $role, int $count) {
    [$a, , $departments] = tenantAccessFixture();
    $user = User::factory()->create([
        'role' => $role, 'institution_id' => $role === 'superadmin' ? null : $a,
        'department_id' => in_array($role, ['operator']) ? $departments[0] : null,
    ]);
    $this->actingAs($user);

    expect(ItemUnit::count())->toBe($count)
        ->and(Department::count())->toBe($count)
        ->and(Institution::count())->toBe($role === 'superadmin' ? 2 : 1)
        ->and(Category::count())->toBe($role === 'superadmin' ? 3 : 2);
})->with([
    ['superadmin', 3], ['admin', 2], ['operator', 1],
]);

test('unauthenticated inventory queries fail closed', function () {
    tenantAccessFixture();
    expect(ItemUnit::count())->toBe(0)->and(Institution::count())->toBe(0);
});

test('operators cannot fetch or change another department item', function () {
    [$a, , $departments, , $items] = tenantAccessFixture();
    $user = User::factory()->create([
        'role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0],
    ]);
    $this->actingAs($user);
    expect(ItemUnit::find($items[1]))->toBeNull()->and(ItemUnit::find($items[2]))->toBeNull();
    $other = ItemUnit::withoutGlobalScopes()->findOrFail($items[1]);
    expect(Gate::allows('update', $other))->toBeFalse();
    $other->serial_number = 'Unauthorized';
    expect(fn () => $other->save())->toThrow(AuthorizationException::class);
});

test('operators can update own items but cannot move them to another department', function () {
    [$a, , $departments, , $items] = tenantAccessFixture();
    $user = User::factory()->create([
        'role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0],
    ]);
    $this->actingAs($user);
    $item = ItemUnit::findOrFail($items[0]);
    expect(Gate::allows('update', $item))->toBeTrue();
    $item->serial_number = 'Updated';
    $item->save();
    $item->department_id = $departments[1];
    expect(fn () => $item->save())->toThrow(AuthorizationException::class);
});

test('user assignment rejects mismatched role institution or department', function (string $role, string $placement) {
    [$a, $b, $departments] = tenantAccessFixture();
    expect(fn () => User::factory()->create([
        'role' => $role, 'institution_id' => $placement === 'missing' ? null : $a,
        'department_id' => $placement === 'other' ? $departments[2] : ($placement === 'present' ? $departments[0] : null),
    ]))->toThrow(ValidationException::class);
})->with([
    ['operator', 'missing'], ['operator', 'other'], ['admin', 'present'], ['superadmin', 'present'],
]);

test('database rejects a user assigned to another institution department', function () {
    [$a, , $departments] = tenantAccessFixture();
    expect(fn () => DB::table('users')->insert([
        'name' => 'Invalid', 'email' => 'invalid@example.com', 'password' => 'unused',
        'role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[2],
    ]))->toThrow(QueryException::class);
});

test('institution admins can manage only their own field accounts', function () {
    [$a, $b, $departments] = tenantAccessFixture();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $a, 'department_id' => null]);
    $own = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $other = User::factory()->create(['role' => 'operator', 'institution_id' => $b, 'department_id' => $departments[2]]);
    $this->actingAs($admin);

    expect(User::query()->visibleTo($admin)->pluck('id')->all())->toContain($own->id)->not->toContain($other->id);
    expect(Gate::allows('update', $own))->toBeTrue()->and(Gate::allows('update', $other))->toBeFalse();
    $own->role = 'superadmin';
    $own->institution_id = null;
    $own->department_id = null;
    expect(fn () => $own->save())->toThrow(AuthorizationException::class);
});

test('invalid or inactive assignments block home but allow logout', function (string $reason) {
    $user = User::factory()->create();
    if ($reason === 'missing') {
        DB::table('users')->where('id', $user->id)->update(['institution_id' => null, 'department_id' => null]);
    } elseif ($reason === 'institution') {
        DB::table('institutions')->where('id', $user->institution_id)->update(['is_active' => false]);
    } else {
        DB::table('departments')->where('id', $user->department_id)->update(['is_active' => false]);
    }
    $this->actingAs($user->fresh());
    $this->get(route('home'))->assertForbidden();
    expect(ItemUnit::count())->toBe(0);
    $this->post(route('logout'))->assertRedirect(route('login'));
})->with(['missing', 'institution', 'department']);

test('an operator cannot promote their own authenticated model', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $this->actingAs($user);
    $user->role = 'superadmin';
    $user->institution_id = null;
    $user->department_id = null;
    expect(fn () => $user->save())->toThrow(AuthorizationException::class);
    expect($user->fresh()->role)->toBe('operator');
});

test('institution user relations respect department access', function () {
    [$a, , $departments] = tenantAccessFixture();
    $own = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[0]]);
    $other = User::factory()->create(['role' => 'operator', 'institution_id' => $a, 'department_id' => $departments[1]]);
    $this->actingAs($own);

    expect($own->institution->users->pluck('id')->all())->toBe([$own->id]);
    expect($own->department->users->pluck('id')->all())->not->toContain($other->id);
});

test('creation policies restrict shared administration to admins', function () {
    $user = User::factory()->create(['role' => 'operator']);
    $this->actingAs($user);
    expect(Gate::allows('create', ItemUnit::class))->toBeTrue()
        ->and(Gate::allows('create', Item::class))->toBeFalse()
        ->and(Gate::allows('create', Category::class))->toBeFalse()
        ->and(Gate::allows('create', Department::class))->toBeFalse()
        ->and(Gate::allows('create', Institution::class))->toBeFalse()
        ->and(Gate::allows('create', User::class))->toBeFalse();
});

test('admins cannot transfer inventory between institutions', function () {
    [$a, $b, $departments, $categories, $items] = tenantAccessFixture();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $a, 'department_id' => null]);
    $this->actingAs($admin);
    $item = ItemUnit::findOrFail($items[0]);
    $item->institution_id = $b;
    $item->department_id = $departments[2];

    expect(fn () => $item->save())->toThrow(AuthorizationException::class);
});

test('superadmins can manage inventory and accounts across institutions', function () {
    [, $b, , , $items] = tenantAccessFixture();
    $superadmin = User::factory()->superadmin()->create();
    $target = User::factory()->create(['role' => 'admin', 'institution_id' => $b, 'department_id' => null]);
    $this->actingAs($superadmin);
    expect(Gate::allows('update', $target))->toBeTrue();
    $item = ItemUnit::findOrFail($items[2]);
    $item->serial_number = 'Updated by superadmin';
    $item->save();
    expect($item->fresh()->serial_number)->toBe('Updated by superadmin');
});
