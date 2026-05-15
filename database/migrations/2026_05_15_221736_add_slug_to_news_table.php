<?php

use App\Models\News;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('title');
        });

        News::withoutTimestamps(function () {
            News::all()->each(function (News $news) {
                $news->slug = $this->uniqueSlug($news->title, $news->id);
                $news->saveQuietly();
            });
        });

        Schema::table('news', function (Blueprint $table) {
            $table->string('slug')->nullable(false)->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }

    private function uniqueSlug(string $title, int $excludeId): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 2;

        while (News::where('slug', $slug)->where('id', '!=', $excludeId)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
};
