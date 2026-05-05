<?php

namespace App\Models\Wdr;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Participant extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'participants';

    public $timestamps = false; // <-- Add this

    // protected $casts = [
    //     'date_of_birth' => 'date',
    // ];

    protected $appends = ['date_of_birth_dmy'];

    public function getDateOfBirthDmyAttribute()
    {
    return $this->date_of_birth
        ? Carbon::parse($this->date_of_birth)->format('d/m/Y')
        : null;
    }


    public function guardian()
    {
        return $this->belongsTo(Guardian::class, 'guardian_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function pantSize()
    {
        return $this->belongsTo(SizeLookup::class, 'pants_size_id', 'id');
    }

    public function jerseySize()
    {
        return $this->belongsTo(SizeLookup::class, 'jersey_size_id', 'id');
    }

    public function jacketSize()
    {
        return $this->belongsTo(SizeLookup::class, 'jacket_size_id', 'id');
    }

    public function shoeSize()
    {
        return $this->belongsTo(SizeLookup::class, 'shoe_size_id', 'id');
    }

    public function documents()
    {
        return $this->hasMany(ParticipantDocument::class);
    }

    public function qidDocument()
    {
        return $this->hasOne(ParticipantDocument::class, 'participant_id', 'id')
            ->where('category', 'qid');
    }

    public function participantType()
    {
        return $this->belongsTo(ParticipantType::class, 'participant_type_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function status()
    {
        return $this->belongsTo(ParticipantStatus::class, 'status_id');
    }
}
