<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->boolean('demo_enabled')->default(false);
            $table->boolean('demo_public')->default(false);
            $table->boolean('is_template')->default(false);
            $table->boolean('download_enabled')->default(false);
            $table->boolean('source_code_enabled')->default(false);
            $table->string('download_url', 2048)->nullable();
            $table->string('source_code_url', 2048)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn([
                'demo_enabled',
                'demo_public',
                'is_template',
                'download_enabled',
                'source_code_enabled',
                'download_url',
                'source_code_url',
            ]);
        });
    }
};
