<?php

namespace App\Models\Swr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantStatus extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'participant_statuses';

     public $timestamps = false; // 🔹 prevent Eloquent from looking for created_at/updated_at
}
