<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    public function Role() {
        return $this->belongsTo(Role::class);
    }

    public function Company() {
        return $this->belongsTo(Company::class);
    }
}
