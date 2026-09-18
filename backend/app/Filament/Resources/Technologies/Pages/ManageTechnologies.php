<?php

namespace App\Filament\Resources\Technologies\Pages;

use App\Filament\Resources\Technologies\TechnologyResource;
use App\Filament\Resources\Support\ContentActions;
use App\Models\Technology;
use App\Services\Admin\Content\CatalogContentService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageTechnologies extends ManageRecords
{
    protected static string $resource = TechnologyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(CatalogContentService::class, Technology::class),
        ];
    }
}
