<?php

namespace App\Models\Wdr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuardianDocument extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'guardian_documents';

    // protected $fillable = [
    //     'participant_id','category','disk','path','original_name','mime','size','created_by'
    // ];

    public function guardian()
    {
        return $this->belongsTo(Guardian::class);
    }
}
