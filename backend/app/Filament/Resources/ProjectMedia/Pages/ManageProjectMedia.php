<?php

namespace App\Filament\Resources\ProjectMedia\Pages;

use App\Filament\Resources\ProjectMedia\ProjectMediaResource;
use App\Filament\Resources\Support\ContentActions;
use App\Services\Admin\Content\ProjectMediaContentService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProjectMedia extends ManageRecords
{
    protected static string $resource = ProjectMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::create(ProjectMediaContentService::class),
        ];
    }
}
