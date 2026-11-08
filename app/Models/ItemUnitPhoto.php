<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $path
 */
class ItemUnitPhoto extends Model
{
    public $timestamps = false;

    protected $fillable = ['path'];

    protected $hidden = ['path'];
}
