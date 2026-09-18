<?php

namespace App\Models;

use Database\Factories\DailyAnalyticsSummaryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyAnalyticsSummary extends Model
{
    /** @use HasFactory<DailyAnalyticsSummaryFactory> */
    use HasFactory;

    protected $table = 'analytics_daily_summaries';

    protected $fillable = ['date', 'metric', 'dimension', 'value'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'value' => 'integer',
        ];
    }
}
