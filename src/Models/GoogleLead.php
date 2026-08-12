<?php

namespace Vendor\MetaLeadIngester\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoogleLead extends Model
{
    protected $fillable = [
        'google_account_id',
        'lead_id',
        'form_id',
        'campaign_id',
        'full_name',
        'email',
        'phone_number',
        'raw_field_data',
        'lead_created_at',
    ];

    protected $casts = [
        'raw_field_data' => 'array',
        'lead_created_at' => 'datetime',
    ];

    /**
     * Get the account this lead belongs to.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(GoogleAccount::class, 'google_account_id');
    }
}
