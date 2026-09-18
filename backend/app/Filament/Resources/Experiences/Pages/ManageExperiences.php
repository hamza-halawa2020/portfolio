<?php

namespace App\Filament\Resources\Experiences\Pages;

use App\Filament\Resources\Experiences\ExperienceResource;
use App\Filament\Resources\Support\ContentActions;
use App\Models\Experience;
use App\Services\Admin\Content\ProfileContentService;
use Filament\Resources\Pages\ManageRecords;

class ManageExperiences extends ManageRecords
{
    protected static string $resource = ExperienceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(ProfileContentService::class, Experience::class),
        ];
    }
}
