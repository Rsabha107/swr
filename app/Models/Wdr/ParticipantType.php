<?php

namespace App\Models\Wdr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantType extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'participant_types';
}
