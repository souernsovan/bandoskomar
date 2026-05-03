<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Provider extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'image',
    ];
}
