<?php

namespace App\Filament\Resources\Skills\Pages;

use App\Filament\Resources\Skills\SkillResource;
use App\Filament\Resources\Support\ContentActions;
use App\Models\Skill;
use App\Services\Admin\Content\ProfileContentService;
use Filament\Resources\Pages\ManageRecords;

class ManageSkills extends ManageRecords
{
    protected static string $resource = SkillResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ContentActions::createForModel(ProfileContentService::class, Skill::class),
        ];
    }
}
