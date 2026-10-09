<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('content');
            $table->timestamp('published_at')->nullable();
            $table->boolean('visible')->default(true);
            $table->timestamps();
            $table->index(['project_id', 'visible', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_updates');
    }
};
