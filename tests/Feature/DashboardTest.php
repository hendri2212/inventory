<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

test('dashboard counts and loan details follow the signed-in account scope', function (string $role, int $total, int $available, ?int $users, int $loans, float $value) {
    [$first, $second, $departments, , $items] = $fixture = unitFixture();

    $superadmin = User::factory()->superadmin()->create();
    $admin = User::factory()->create(['role' => 'admin', 'institution_id' => $first, 'department_id' => null]);
    $operator = User::factory()->create(['role' => 'operator', 'institution_id' => $first, 'department_id' => $departments[0]]);
    User::factory()->create(['role' => 'admin', 'institution_id' => $second, 'department_id' => null]);

    $payload = [...unitPayload($fixture), 'disposed_at' => null];
    $borrowed = DB::table('item_units')->insertGetId($payload);
    $returned = DB::table('item_units')->insertGetId([...$payload, 'code' => 'UNIT-002']);
    DB::table('item_units')->insert([
        [...$payload, 'code' => 'INACTIVE', 'is_active' => false],
        [...$payload, 'code' => 'DISPOSED', 'disposed_at' => now(), 'purchase_price' => null],
        [...$payload, 'code' => 'DAMAGED', 'condition' => 'major_damage'],
    ]);

    $otherDepartmentUnit = DB::table('item_units')->insertGetId([
        ...$payload, 'code' => 'OTHER-DEPT', 'department_id' => $departments[1], 'item_id' => $items[1],
        'purchase_price' => '25000000.25',
    ]);
    $otherInstitutionUnit = DB::table('item_units')->insertGetId([
        ...$payload, 'code' => 'OTHER-INST', 'institution_id' => $second, 'department_id' => $departments[2],
        'item_id' => $items[2], 'room_id' => null, 'purchase_price' => '500000.75',
    ]);

    foreach ([
        [$first, $departments[0], 'LOAN-OWN', [$borrowed, $returned], true],
        [$first, $departments[1], 'LOAN-OTHER-DEPT', [$otherDepartmentUnit], false],
        [$second, $departments[2], 'LOAN-OTHER-INST', [$otherInstitutionUnit], false],
    ] as [$institution, $department, $code, $unitIds, $hasOutstanding]) {
        $loan = DB::table('loans')->insertGetId([
            'institution_id' => $institution, 'department_id' => $department, 'code' => $code,
            'borrower_name' => $code, 'borrowed_at' => now()->subDays($hasOutstanding ? 0 : 1),
            'recorded_by' => $superadmin->id,
        ]);
        foreach ($unitIds as $index => $unit) {
            $outstanding = $hasOutstanding && $index === 0;
            DB::table('loan_items')->insert([
                'institution_id' => $institution, 'loan_id' => $loan, 'item_unit_id' => $unit,
                'active_item_unit_id' => $outstanding ? $unit : null,
                'returned_at' => $outstanding ? null : now(), 'condition_out' => 'good',
            ]);
        }
    }

    $this->actingAs(match ($role) {
        'admin' => $admin,
        'operator' => $operator,
        default => $superadmin,
    });

    $this->get('/?menu=dashboard')->assertOk()->assertInertia(fn (Assert $page) => $page
        ->component('Welcome')
        ->where('summary.total_units', $total)
        ->where('summary.total_purchase_value', fn (string $total) => (float) $total === $value)
        ->where('summary.loaned_units', 1)
        ->where('summary.available_units', $available)
        ->where('summary.total_users', $users)
        ->where('summary.item_types', $role === 'operator' ? 1 : null)
        ->has('loans', $loans)
        ->where('loans.0.code', 'LOAN-OWN')
        ->where('loans.0.unit_count', 2)
        ->where('loans.0.returned_count', 1));
})->with([
    'superadmin' => ['superadmin', 7, 3, 4, 3, 75500003.00],
    'admin' => ['admin', 6, 2, 2, 2, 75000002.25],
    'operator' => ['operator', 5, 1, null, 1, 50000002.00],
]);

test('dashboard returns an empty inventory for a newly assigned operator', function () {
    $this->actingAs(User::factory()->create());
    $this->get('/')->assertInertia(fn (Assert $page) => $page
        ->where('summary.total_units', 0)
        ->where('summary.total_purchase_value', '0')
        ->where('summary.loaned_units', 0)
        ->where('summary.available_units', 0)
        ->has('loans', 0));
});
