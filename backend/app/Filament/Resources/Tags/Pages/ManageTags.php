<?php

namespace App\Filament\Resources\Tags\Pages;

use App\Filament\Resources\Support\ContentActions;
use App\Filament\Resources\Tags\TagResource;
use App\Models\Tag;
use App\Services\Admin\Content\LocalizedRecordService;
use Filament\Resources\Pages\ManageRecords;

class ManageTags extends ManageRecords
{
    protected static string $resource = TagResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(LocalizedRecordService::class, Tag::class),
        ];
    }
}
