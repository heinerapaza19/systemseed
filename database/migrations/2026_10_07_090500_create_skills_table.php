<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable()->index();
            $table->string('level')->nullable();
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('visible')->default(true)->index();
            $table->timestamps();

            $table->index(['visible', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
