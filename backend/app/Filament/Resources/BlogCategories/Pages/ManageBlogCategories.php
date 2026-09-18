<?php

namespace App\Filament\Resources\BlogCategories\Pages;

use App\Filament\Resources\BlogCategories\BlogCategoryResource;
use App\Filament\Resources\Support\ContentActions;
use App\Models\BlogCategory;
use App\Services\Admin\Content\LocalizedRecordService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBlogCategories extends ManageRecords
{
    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(LocalizedRecordService::class, BlogCategory::class),
        ];
    }
}
