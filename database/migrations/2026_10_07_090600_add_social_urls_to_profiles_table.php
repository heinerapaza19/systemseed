<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('instagram_url')->nullable()->after('website_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            $table->string('tiktok_url')->nullable()->after('youtube_url');
            $table->string('whatsapp_url')->nullable()->after('tiktok_url');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['instagram_url', 'youtube_url', 'tiktok_url', 'whatsapp_url']);
        });
    }
};
