<?php

namespace App\Models\Wdr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryNumGen extends Model
{
    use HasFactory;
    protected $table = 'rs_number_gen';
    protected $guarded = [];

}
