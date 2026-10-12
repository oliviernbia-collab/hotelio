<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = ['user_id', 'subscription_plan_id', 'status', 'started_at', 'current_period_end', 'cancelled_at', 'jeko_reference', 'jeko_payment_request_id'];

    protected $casts = [
        'started_at'          => 'date',
        'current_period_end'  => 'date',
        'cancelled_at'        => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'subscription_plan_id');
    }

    public function payments()
    {
        return $this->hasMany(SubscriptionPayment::class);
    }
}
