<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostResource;
use App\Filament\Resources\Support\ContentActions;
use App\Services\Admin\Content\BlogPostContentService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBlogPosts extends ManageRecords
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::create(BlogPostContentService::class),
        ];
    }
}
