<?php

namespace App\Services\Admin\Content;

use App\Models\Experience;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProfileContentService
{
    public function __construct(private readonly ContentPayload $payload) {}

    /**
     * @param  class-string<Model>  $modelClass
     * @param  array<string, mixed>  $data
     */
    public function create(string $modelClass, array $data): Model
    {
        return $this->save(new $modelClass, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Model $record, array $data): Model
    {
        return $this->save($record, $data);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function save(Model $record, array $data): Model
    {
        $localized = match ($record::class) {
            Experience::class => ['title', 'company', 'location', 'description'],
            Skill::class => ['name'],
            default => [],
        };

        $data = $this->payload->sanitizeLocalizedHtml($data, ['description']);
        $data = $this->payload->mergeLocalized($record, $data, $localized);

        if ($record instanceof Skill && blank($data['group'] ?? null)) {
            $data['group'] = Str::slug((string) ($data['name']['en'] ?? 'general'));
        }

        $record->fill($data);
        $record->save();

        return $record;
    }
}
