<?php

namespace App\Models\Swr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'venues';

    public function vapp_variations()
    {
        return $this->belongsToMany(VappVariation::class, 'vapp_variation_venue', 'venue_id', 'vapp_variation_id');
    }
    public function matches()
    {
        return $this->hasMany(MatchList::class, 'venue_id');
    }
}
