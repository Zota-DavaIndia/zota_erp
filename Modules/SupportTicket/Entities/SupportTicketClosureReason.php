<?php

namespace Modules\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;

class SupportTicketClosureReason extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'requires_resend' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Reasons available to a business: the global (business_id null) set
     * plus any the business has added for itself.
     */
    public function scopeForBusiness($query, $business_id)
    {
        return $query->where(function ($q) use ($business_id) {
            $q->whereNull('business_id')->orWhere('business_id', $business_id);
        });
    }
}
