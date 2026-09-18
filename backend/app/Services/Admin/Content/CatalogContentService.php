<?php

namespace App\Services\Admin\Content;

use App\Models\Technology;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CatalogContentService
{
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
        if ($record instanceof Technology && blank($data['slug'] ?? null)) {
            $data['slug'] = Str::slug((string) ($data['name'] ?? 'technology'));
        }

        $record->fill($data);
        $record->save();

        return $record;
    }
}
