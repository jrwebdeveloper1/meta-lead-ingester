<?php

namespace Vendor\MetaLeadIngester\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetaLead extends Model
{
    protected $fillable = [
        'meta_account_id',
        'leadgen_id',
        'form_id',
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
     * Get the Meta Account that owns the lead.
     *
     * @return BelongsTo
     */
    public function metaAccount(): BelongsTo
    {
        return $this->belongsTo(MetaAccount::class);
    }
}
