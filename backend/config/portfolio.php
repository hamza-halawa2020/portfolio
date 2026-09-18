<?php

return [
    'visitor' => [
        'cookie' => env('PUBLIC_VISITOR_COOKIE', 'portfolio_visitor'),
        'hash_secret' => env('VISITOR_HASH_SECRET'),
        'lifetime_minutes' => (int) env('PUBLIC_VISITOR_COOKIE_LIFETIME', 525600),
        'domain' => env('PUBLIC_VISITOR_COOKIE_DOMAIN'),
    ],

    'rate_limits' => [
        'project_views' => (int) env('RATE_LIMIT_PROJECT_VIEWS_PER_MINUTE', 60),
        'project_likes' => (int) env('RATE_LIMIT_PROJECT_LIKES_PER_MINUTE', 30),
        'testimonials' => (int) env('RATE_LIMIT_TESTIMONIALS_PER_HOUR', 5),
        'contact' => (int) env('RATE_LIMIT_CONTACT_PER_HOUR', 5),
    ],

    'media' => [
        'disk' => env('PORTFOLIO_MEDIA_DISK', 'public'),
        'image_mimes' => ['image/jpeg', 'image/png', 'image/webp', 'image/avif'],
        'video_mimes' => ['video/mp4', 'video/webm'],
        'max_image_kb' => (int) env('PORTFOLIO_MAX_IMAGE_KB', 5120),
        'max_video_kb' => (int) env('PORTFOLIO_MAX_VIDEO_KB', 51200),
    ],
];
