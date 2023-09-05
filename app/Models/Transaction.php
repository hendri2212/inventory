<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    public function Good() {
        return $this->belongsTo(Good::class);
    }

    public function Supplier() {
        return $this->belongsTo(Supplier::class);
    }

    public function Room() {
        return $this->belongsTo(Room::class);
    }
}
