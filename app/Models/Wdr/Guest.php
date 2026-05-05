<?php

namespace App\Models\Wdr;

use App\Models\Wdr\Designation;
use App\Models\Wdr\Event;
use App\Models\Wdr\Nationality;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    //
    use HasFactory;
    protected $guarded = [];
    protected $table = 'guests';

    public static function boot(){

        parent::boot();

        try {
            static::creating(function($model){
                $numGen = DeliveryNumGen::first();
                if ($numGen == null) {
                    $numGen = new DeliveryNumGen();
                    $numGen->last_number = 0;
                    $numGen->save();
                }
                $last_number = $numGen->max('last_number') + 1;
                $numGen->update(['last_number' => $last_number]);

                $model->ref_number = 'GMS'.'-'.str_pad($last_number, 5, '0', STR_PAD_LEFT);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            $errorInfo = $e->errorInfo;
            return redirect()->back()->with('error', $errorInfo[2]);
            // dd($e->getMessage());
        }
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function flights()
    {
        return $this->hasMany(GuestFlight::class, 'guest_id', 'id');
    }

    public function accommodations()
    {
        return $this->hasMany(GuestAccommodation::class, 'guest_id', 'id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function guest_type()
    {
        return $this->belongsTo(GuestType::class, 'guest_type_id');
    }
    public function prefix()
    {
        return $this->belongsTo(Prefix::class, 'prefix_id');
    }
    public function client_group()
    {
        return $this->belongsTo(ClientGroup::class, 'client_group_id');
    }
    public function hosted_by()
    {
        return $this->belongsTo(HostedBy::class, 'hosted_by_id');
    }
    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }
    public function getFullNameWithPrefixAttribute()
    {
        return $this->prefix->name . ' ' . $this->first_name . ' ' . $this->last_name;
    }
    public function getFullNameWithPrefixAndDesignationAttribute()
    {
        return $this->prefix->name . ' ' . $this->first_name . ' ' . $this->last_name . ' (' . $this->designation->name . ')';
    }
    public function getFullNameAttribute()
    {
        return $this->prefix->name . ' ' . $this->first_name . ' ' . $this->last_name;
    }
    public function getFullNameWithDesignationAttribute()
    {
        return $this->prefix->name . ' ' . $this->first_name . ' ' . $this->last_name . ' (' . $this->designation->name . ')';
    }
  
}
