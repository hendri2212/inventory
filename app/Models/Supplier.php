<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'company_id',
        'image',
        'telephone',
        'address'
    ];

    public function Company() {
        return $this->belongsTo(Company::class);
    }
}
