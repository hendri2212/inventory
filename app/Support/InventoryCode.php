<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InventoryCode
{
    // Call inside the transaction that saves the new record.
    public static function next(string $table, int $institutionId, ?int $departmentId = null): string
    {
        $institution = DB::table('institutions')->where('id', $institutionId)->lockForUpdate()->first();
        $prefix = $departmentId === null ? $institution->code : DB::table('departments')->where('institution_id', $institutionId)->where('id', $departmentId)->value('code');
        $max = 0;
        foreach (DB::table($table)->where('institution_id', $institutionId)->pluck('code') as $code) {
            if (preg_match('/^'.preg_quote($prefix, '/').'-(\d+)$/i', $code, $match)) {
                $max = max($max, (int) $match[1]);
            }
        }
        $code = $prefix.'-'.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
        if (mb_strlen($code) > 50) {
            throw ValidationException::withMessages([$departmentId === null ? 'institution_id' : 'department_id' => 'Kode '.($departmentId === null ? 'instansi' : 'bidang').' terlalu panjang untuk membuat kode otomatis.']);
        }

        return $code;
    }
}
