<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'page_category', 'content', 'meta_description', 'meta_keywords', 'status', 'icon'];

    protected $casts = [
        'content' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
