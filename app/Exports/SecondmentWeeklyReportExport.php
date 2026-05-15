<?php

namespace App\Exports;

use App\Models\Swr\SecondmentWeeklyReport;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SecondmentWeeklyReportExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function headings(): array
    {
        return [
            'REFERENCE NUMBER',
            'REPORTING WEEK',
            'EVENT',
            'VENUE',
            'CITY',
            'NAME',
            'ROLE',
            'MAIN ACTIVITIES',
            'EXPERIENCE GAINED',
            'INNOVATION DESCRIPTION',
            'INNOVATION FUNCTIONAL AREAS',
            'INNOVATION OTHER AREA',
            'CHALLENGES DESCRIPTION',
            'CHALLENGES RESOLVED',
            'CHALLENGE FUNCTIONAL AREAS',
            'CHALLENGES OTHER AREA',
            'VALUE FOR QATAR',
            'VALUE FOR QATAR TYPE',
            'VALUE FOR QATAR DESCRIPTION',
            'WELLBEING STATUS',
            'NEEDS SUPPORT',
            'SUPPORT TYPES',
            'SUPPORT OTHER DESCRIPTION',
            'ADDITIONAL COMMENT',
            'STATUS',
            'CREATED AT',
        ];
    }

    public function collection()
    {
        $query = SecondmentWeeklyReport::with([
            'event', 
            'venue', 
            'user',
            'innovationFunctionalAreas.functionalArea',
            'challengeFunctionalAreas.functionalArea'
        ]);

        // Apply filters
        if (!empty($this->filters['export_event_filter'])) {
            $query->whereIn('event_id', $this->filters['export_event_filter']);
        }

        if (!empty($this->filters['export_venue_filter'])) {
            $query->whereIn('venue_id', $this->filters['export_venue_filter']);
        }

        if (!empty($this->filters['export_date_range_filter'])) {
            $dateRange = explode(' to ', $this->filters['export_date_range_filter']);
            if (count($dateRange) == 2) {
                $query->whereBetween('reporting_week', [$dateRange[0], $dateRange[1]]);
            }
        }

        $reports = $query->get();

        $reports->transform(function ($report) {
            // Get innovation functional areas
            $innovationAreas = $report->innovationFunctionalAreas
                ->pluck('functionalArea.fa_code')
                ->filter()
                ->join(', ');

            // Get challenge functional areas
            $challengeAreas = $report->challengeFunctionalAreas
                ->pluck('functionalArea.fa_code')
                ->filter()
                ->join(', ');

            // Format support types array
            $supportTypes = is_array($report->support_types) 
                ? implode(', ', $report->support_types) 
                : $report->support_types;

            return [
                'reference_number' => $report->reference_number,
                'reporting_week' => format_date($report->reporting_week),
                'event' => $report->event?->name,
                'venue' => $report->venue?->title,
                'city' => $report->city,
                'name' => $report->name,
                'role' => $report->role,
                'main_activities' => $report->main_activities,
                'experience_gained' => $report->experience_gained,
                'innovation_description' => $report->innovation_description,
                'innovation_functional_areas' => $innovationAreas,
                'innovation_other_area' => $report->innovation_other_area,
                'challenges_description' => $report->challenges_description,
                'challenges_resolved' => $report->challenges_resolved ? 'Yes' : 'No',
                'challenge_functional_areas' => $challengeAreas,
                'challenges_other_area' => $report->challenges_other_area,
                'value_for_qatar' => $report->value_for_qatar ? 'Yes' : 'No',
                'value_for_qatar_type' => $report->value_for_qatar_type,
                'value_for_qatar_description' => $report->value_for_qatar_description,
                'wellbeing_status' => $report->wellbeing_status,
                'needs_support' => $report->needs_support ? 'Yes' : 'No',
                'support_types' => $supportTypes,
                'support_other_description' => $report->support_other_description,
                'additional_comment' => $report->additional_comment,
                'status' => $report->status,
                'created_at' => $report->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return $reports;
    }
}
