<?php

namespace App\Models\Swr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SecondmentWeeklyReport extends Model
{
    
    protected $fillable = [
        'reference_number',
        'user_id',
        'event_id',
        'venue_id',
        'reporting_week',
        'name',
        'role',
        'city',
        'main_activities',
        'experience_gained',
        'innovation_description',
        'challenges_description',
        'challenges_resolved',
        'value_for_qatar',
        'value_for_qatar_type',
        'value_for_qatar_description',
        'wellbeing_status',
        'needs_support',
        'support_types',
        'support_other_description',
        'additional_comment',
        'status',
    ];

    protected $casts = [
        'reporting_week' => 'date',
        'challenges_resolved' => 'boolean',
        'value_for_qatar' => 'boolean',
        'needs_support' => 'boolean',
        'support_types' => 'array',
    ];

    // Relationships
    
    /**
     * The user who submitted this report
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * The event this report belongs to
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Swr\Event::class);
    }

    /**
     * The venue this report is for
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Swr\Venue::class);
    }

    /**
     * Documents (photos) attached to this report
     */
    public function documents(): HasMany
    {
        return $this->hasMany(SecondmentWeeklyReportDocument::class);
    }

    /**
     * Innovation functional areas relationship
     */
    public function innovationFunctionalAreas()
    {
        return $this->hasMany(SecondmentWeeklyReportInnovationFunctionalArea::class, 'secondment_weekly_report_id');
    }

    /**
     * Challenge functional areas relationship
     */
    public function challengeFunctionalAreas()
    {
        return $this->hasMany(SecondmentWeeklyReportChallengeFunctionalArea::class, 'secondment_weekly_report_id');
    }

    // Query Scopes
    
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForEvent($query, $eventId)
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeForVenue($query, $venueId)
    {
        return $query->where('venue_id', $venueId);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    // Accessors/Methods
    
    public function canEdit()
    {
        return $this->status === 'draft';
    }

    public function getStatusLabel()
    {
        $labels = [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getWellbeingEmoji()
    {
        $emojis = [
            'Good' => '😊',
            'Moderate' => '😐',
            'Challenging' => '😟',
        ];
        return $emojis[$this->wellbeing_status] ?? '❓';
    }
}
