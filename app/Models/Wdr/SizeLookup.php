<?php

namespace App\Models\Wdr;

use Illuminate\Database\Eloquent\Model;

class SizeLookup extends Model
{
    protected $fillable = [
        'type', 'code', 'label', 'gender', 'sort_order', 'active'
    ];

    public function scopeType($query, $type)
    {
        return $query->where('type', $type)->where('active', 1)->orderBy('sort_order');
    }
}
