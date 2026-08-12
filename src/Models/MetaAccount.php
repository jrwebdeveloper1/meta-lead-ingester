<?php

namespace Vendor\MetaLeadIngester\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaAccount extends Model
{
    protected $fillable = [
        'company_name',
        'page_id',
        'page_access_token',
        'verify_token',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the leads associated with the Meta Account.
     *
     * @return HasMany
     */
    public function leads(): HasMany
    {
        return $this->hasMany(MetaLead::class);
    }
}
