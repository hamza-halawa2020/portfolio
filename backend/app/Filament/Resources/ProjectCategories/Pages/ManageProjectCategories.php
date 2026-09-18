<?php

namespace App\Filament\Resources\ProjectCategories\Pages;

use App\Filament\Resources\ProjectCategories\ProjectCategoryResource;
use App\Filament\Resources\Support\ContentActions;
use App\Models\ProjectCategory;
use App\Services\Admin\Content\LocalizedRecordService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectCategories extends ManageRecords
{
    protected static string $resource = ProjectCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(LocalizedRecordService::class, ProjectCategory::class),
        ];
    }
}
