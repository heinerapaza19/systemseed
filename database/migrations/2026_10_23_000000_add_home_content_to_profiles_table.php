<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('about_title')->nullable()->after('bio');
            $table->string('journey_title')->nullable()->after('about_title');
            $table->text('journey_intro')->nullable()->after('journey_title');
            $table->text('experience_empty_text')->nullable()->after('journey_intro');
            $table->text('education_empty_text')->nullable()->after('experience_empty_text');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'about_title',
                'journey_title',
                'journey_intro',
                'experience_empty_text',
                'education_empty_text',
            ]);
        });
    }
};
