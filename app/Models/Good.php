<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Good extends Model
{
    use HasFactory;

    public function Category() {
        return $this->belongsTo(Category::class);
    }
    
    public function Transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
