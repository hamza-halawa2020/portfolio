<?php

namespace App\Models;

use App\Enums\AnalyticsEventType;
use Database\Factories\AnalyticsEventFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticsEvent extends Model
{
    /** @use HasFactory<AnalyticsEventFactory> */
    use HasFactory;

    protected $fillable = ['event_type', 'visitor_id_hash', 'ip_hash', 'user_agent_hash', 'url', 'referrer_domain', 'device_category', 'browser', 'country', 'occurred_at'];

    protected $hidden = ['visitor_id_hash', 'ip_hash', 'user_agent_hash'];

    protected function casts(): array
    {
        return [
            'event_type' => AnalyticsEventType::class,
            'occurred_at' => 'datetime',
        ];
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderByDesc('occurred_at')->orderByDesc('id');
    }
}
