<?php

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

function inventoryFixture(): array
{
    $institution = DB::table('institutions')->insertGetId(['code' => 'SCH-A', 'name' => 'Sekolah A']);
    $otherInstitution = DB::table('institutions')->insertGetId(['code' => 'SCH-B', 'name' => 'Sekolah B']);
    $department = DB::table('departments')->insertGetId([
        'institution_id' => $institution, 'code' => 'TU', 'name' => 'Tata Usaha',
    ]);
    $category = DB::table('categories')->insertGetId([
        'institution_id' => $institution, 'name' => 'Elektronik',
    ]);

    return [$institution, $otherInstitution, $department, $category];
}

test('items cannot reference another institution category', function () {
    [$institution, $otherInstitution] = inventoryFixture();
    $category = DB::table('categories')->insertGetId(['institution_id' => $otherInstitution, 'name' => 'Lain']);
    expect(fn () => DB::table('items')->insert([
        'institution_id' => $institution, 'category_id' => $category, 'code' => 'PC-01', 'name' => 'Komputer',
    ]))->toThrow(QueryException::class);
});

test('item codes can repeat across institutions but not inside an institution', function () {
    [$institution, $otherInstitution, $department, $category] = inventoryFixture();
    $item = [
        'institution_id' => $institution,
        'category_id' => $category, 'code' => 'PC-01', 'name' => 'Komputer',
    ];
    DB::table('items')->insert($item);
    $otherDepartment = DB::table('departments')->insertGetId([
        'institution_id' => $otherInstitution, 'code' => 'TU', 'name' => 'Tata Usaha',
    ]);
    $otherCategory = DB::table('categories')->insertGetId([
        'institution_id' => $otherInstitution, 'name' => 'Elektronik',
    ]);
    DB::table('items')->insert(array_replace($item, [
        'institution_id' => $otherInstitution,
        'category_id' => $otherCategory,
    ]));

    expect(DB::table('items')->count())->toBe(2);
    expect(fn () => DB::table('items')->insert($item))->toThrow(QueryException::class);
});

test('referenced inventory parents cannot be deleted', function (string $table) {
    [$institution, , $department, $category] = inventoryFixture();
    $item = DB::table('items')->insertGetId([
        'institution_id' => $institution,
        'category_id' => $category, 'code' => 'PC-01', 'name' => 'Komputer',
    ]);
    DB::table('item_units')->insert(['institution_id' => $institution, 'item_id' => $item, 'department_id' => $department, 'code' => 'UNIT']);
    $id = match ($table) {
        'institutions' => $institution,
        'departments' => $department,
        'categories' => $category,
    };

    expect(fn () => DB::table($table)->where('id', $id)->delete())->toThrow(QueryException::class);
})->with(['institutions', 'departments', 'categories']);
