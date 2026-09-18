<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Filament\Resources\Support\ContentActions;
use App\Models\Service;
use App\Services\Admin\Content\LocalizedRecordService;
use Filament\Resources\Pages\ManageRecords;

class ManageServices extends ManageRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(LocalizedRecordService::class, Service::class),
        ];
    }
}
