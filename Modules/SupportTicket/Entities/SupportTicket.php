<?php

namespace Modules\SupportTicket\Entities;

use Illuminate\Database\Eloquent\Model;

class SupportTicket extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity_damaged' => 'float',
        'quantity_lost' => 'float',
        'closed_at' => 'datetime',
        'tat_due_at' => 'datetime',
    ];

    public function business()
    {
        return $this->belongsTo(\App\Business::class);
    }

    public function location()
    {
        return $this->belongsTo(\App\BusinessLocation::class, 'location_id');
    }

    public function purchase_line()
    {
        return $this->belongsTo(\App\PurchaseLine::class, 'purchase_line_id');
    }

    public function purchase_order_line()
    {
        return $this->belongsTo(\App\PurchaseLine::class, 'purchase_order_line_id');
    }

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    public function purchase_order()
    {
        return $this->belongsTo(\App\Transaction::class, 'purchase_order_id');
    }

    public function closure_reason()
    {
        return $this->belongsTo(\Modules\SupportTicket\Entities\SupportTicketClosureReason::class, 'closure_reason_id');
    }

    public function raised_by_user()
    {
        return $this->belongsTo(\App\User::class, 'raised_by');
    }

    public function closed_by_user()
    {
        return $this->belongsTo(\App\User::class, 'closed_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public static function ticketTypeLabels()
    {
        return [
            'loss_short' => 'Loss / Short',
            'in_transit_damage' => 'In-transit Damage',
            'mixed' => 'Loss & Damage',
        ];
    }

    public static function statusLabels()
    {
        return [
            'open' => 'Open',
            'delayed' => 'Delayed',
            'closed' => 'Closed',
        ];
    }

    /**
     * A ticket is still actionable (can receive logs / be closed) as long as
     * it isn't closed yet - "delayed" is not a terminal state, just a flag.
     */
    public function isClosed()
    {
        return $this->status == 'closed';
    }

    /**
     * Flip any ticket that's past its TAT and still unresolved to "delayed".
     * Called both lazily (on every ticket list view, so it's always current
     * regardless of whether a scheduler is configured) and from the
     * scheduled sweep command.
     *
     * @param  int|null  $business_id  scope to one business, or null for all
     * @return int  number of tickets flagged
     */
    public static function flagOverdueAsDelayed($business_id = null)
    {
        $query = static::where('status', 'open')
            ->whereNotNull('tat_due_at')
            ->where('tat_due_at', '<', now());

        if (! empty($business_id)) {
            $query->where('business_id', $business_id);
        }

        return $query->update(['status' => 'delayed']);
    }

    public static function generateTicketNumber($business_id)
    {
        $count = self::where('business_id', $business_id)->count() + 1;

        return 'ST-'.str_pad($business_id, 3, '0', STR_PAD_LEFT).'-'.str_pad($count, 5, '0', STR_PAD_LEFT);
    }
}
