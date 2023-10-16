<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'role_id',
        'username',
        'password',
        'company_id'
    ];

    public function Role() {
        return $this->belongsTo(Role::class);
    }

    public function Company() {
        return $this->belongsTo(Company::class);
    }
}
