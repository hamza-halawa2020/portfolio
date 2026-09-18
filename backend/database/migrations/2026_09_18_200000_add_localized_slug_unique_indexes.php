<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @var array<string>
     */
    private array $tables = [
        'project_categories',
        'projects',
        'blog_categories',
        'tags',
        'blog_posts',
        'services',
    ];

    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->tables as $table) {
            DB::statement("ALTER TABLE `{$table}` ADD `slug_en` VARCHAR(191) GENERATED ALWAYS AS (NULLIF(JSON_UNQUOTE(JSON_EXTRACT(`slug`, '$.en')), '')) STORED");
            DB::statement("ALTER TABLE `{$table}` ADD `slug_ar` VARCHAR(191) GENERATED ALWAYS AS (NULLIF(JSON_UNQUOTE(JSON_EXTRACT(`slug`, '$.ar')), '')) STORED");

            Schema::table($table, function (Blueprint $table): void {
                $table->unique('slug_en');
                $table->unique('slug_ar');
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropUnique(['slug_en']);
                $table->dropUnique(['slug_ar']);
            });

            DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `slug_en`");
            DB::statement("ALTER TABLE `{$tableName}` DROP COLUMN `slug_ar`");
        }
    }
};
