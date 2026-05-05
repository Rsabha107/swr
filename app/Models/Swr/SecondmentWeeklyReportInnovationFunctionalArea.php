<?php

namespace App\Models\Swr;

use Illuminate\Database\Eloquent\Model;

class SecondmentWeeklyReportInnovationFunctionalArea extends Model
{
    protected $table = 'secondment_weekly_report_innovation_functional_areas';
    
    protected $fillable = ['secondment_weekly_report_id', 'functional_area_id'];
    
    public $timestamps = true;

    public function report()
    {
        return $this->belongsTo(SecondmentWeeklyReport::class, 'secondment_weekly_report_id');
    }

    public function functionalArea()
    {
        return $this->belongsTo(\App\Models\Swr\FunctionalArea::class, 'functional_area_id');
    }
}
