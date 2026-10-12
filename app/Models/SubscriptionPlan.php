<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = ['slug', 'name', 'price_monthly', 'commission_rate', 'max_hotels', 'features', 'sort_order', 'is_active'];

    protected $casts = [
        'price_monthly'   => 'integer',
        'commission_rate' => 'decimal:2',
        'max_hotels'      => 'integer',
        'features'        => 'array',
        'is_active'       => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
