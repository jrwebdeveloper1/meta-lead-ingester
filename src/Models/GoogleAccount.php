<?php

namespace Vendor\MetaLeadIngester\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GoogleAccount extends Model
{
    protected $fillable = [
        'account_name',
        'google_key',
    ];

    /**
     * Get the leads for this account.
     */
    public function leads(): HasMany
    {
        return $this->hasMany(GoogleLead::class);
    }
}
