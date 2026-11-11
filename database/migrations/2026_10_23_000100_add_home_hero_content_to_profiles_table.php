<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('hero_eyebrow')->nullable()->after('professional_title');
            $table->string('hero_title')->nullable()->after('hero_eyebrow');
            $table->string('hero_highlight')->nullable()->after('hero_title');
            $table->text('hero_description')->nullable()->after('hero_highlight');
            $table->string('contact_title')->nullable()->after('education_empty_text');
            $table->text('contact_intro')->nullable()->after('contact_title');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['hero_eyebrow', 'hero_title', 'hero_highlight', 'hero_description', 'contact_title', 'contact_intro']);
        });
    }
};
