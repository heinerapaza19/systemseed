<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('institution');
            $table->text('description')->nullable();
            $table->date('issued_at')->nullable()->index();
            $table->string('credential_id')->nullable();
            $table->string('verification_url')->nullable();
            $table->string('file_url')->nullable();
            $table->string('file_type', 20)->nullable();
            $table->string('category')->nullable()->index();
            $table->boolean('visible')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['visible', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
