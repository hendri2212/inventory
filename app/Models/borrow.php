<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    use HasFactory;

    public function Good() {
        return $this->belongsTo(Good::class);
    }

    public function Room() {
        return $this->belongsTo(Room::class);
    }
}
