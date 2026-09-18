<?php

namespace App\Enums;

enum AnalyticsEventType: string
{
    case PageView = 'page_view';
    case ProjectView = 'project_view';
    case ProjectLike = 'project_like';
    case ContactSubmission = 'contact_submission';

    public function label(): string
    {
        return match ($this) {
            self::PageView => 'Page view',
            self::ProjectView => 'Project view',
            self::ProjectLike => 'Project like',
            self::ContactSubmission => 'Contact submission',
        };
    }
}
