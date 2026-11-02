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

test('items cannot reference another institution department or category', function (string $relation) {
    [$institution, $otherInstitution, $department, $category] = inventoryFixture();
    if ($relation === 'department') {
        $department = DB::table('departments')->insertGetId([
            'institution_id' => $otherInstitution, 'code' => 'TU', 'name' => 'Tata Usaha',
        ]);
    } else {
        $category = DB::table('categories')->insertGetId([
            'institution_id' => $otherInstitution, 'name' => 'Elektronik',
        ]);
    }

    expect(fn () => DB::table('items')->insert([
        'institution_id' => $institution, 'department_id' => $department,
        'category_id' => $category, 'code' => 'PC-01', 'name' => 'Komputer',
    ]))->toThrow(QueryException::class);
})->with(['department', 'category']);

test('item codes can repeat across institutions but not inside an institution', function () {
    [$institution, $otherInstitution, $department, $category] = inventoryFixture();
    $item = [
        'institution_id' => $institution, 'department_id' => $department,
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
        'institution_id' => $otherInstitution, 'department_id' => $otherDepartment,
        'category_id' => $otherCategory,
    ]));

    expect(DB::table('items')->count())->toBe(2);
    expect(fn () => DB::table('items')->insert($item))->toThrow(QueryException::class);
});

test('referenced inventory parents cannot be deleted', function (string $table) {
    [$institution, , $department, $category] = inventoryFixture();
    DB::table('items')->insert([
        'institution_id' => $institution, 'department_id' => $department,
        'category_id' => $category, 'code' => 'PC-01', 'name' => 'Komputer',
    ]);
    $id = match ($table) {
        'institutions' => $institution,
        'departments' => $department,
        'categories' => $category,
    };

    expect(fn () => DB::table($table)->where('id', $id)->delete())->toThrow(QueryException::class);
})->with(['institutions', 'departments', 'categories']);
