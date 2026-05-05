<?php

namespace App\Models\Swr;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Guardian extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'guardians';

    public $timestamps = false; // <-- Add this

    public function participants()
    {
        return $this->hasMany(Participant::class, 'guardian_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        public function qidDocument()
    {
        return $this->hasOne(GuardianDocument::class, 'guardian_id', 'id')
            ->where('category', 'qid');
    }
}
