<?php

use App\Exceptions\PublicApi\PublishedBlogPostNotFound;
use App\Exceptions\PublicApi\PublishedProjectNotFound;
use App\Jobs\Analytics\AggregateDailyAnalyticsJob;
use App\Jobs\Analytics\CleanupAnalyticsEventsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\HandleCors;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(HandleCors::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->job(new AggregateDailyAnalyticsJob)
            ->dailyAt('00:10')
            ->name('analytics.aggregate-daily');

        $schedule->job(new CleanupAnalyticsEventsJob)
            ->dailyAt('00:30')
            ->name('analytics.cleanup-events');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(fn (PublishedProjectNotFound|PublishedBlogPostNotFound $exception) => response()->json([
            'message' => 'Not found.',
        ], 404));
    })->create();
