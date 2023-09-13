<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public function Company() {
        return $this->belongsTo(Company::class);
    }

    public function Good() {
        return $this->belongsTo(Good::class);
    }

    public function Supplier() {
        return $this->belongsTo(Supplier::class);
    }
}
