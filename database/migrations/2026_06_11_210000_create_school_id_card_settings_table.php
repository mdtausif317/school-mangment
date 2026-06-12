<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_id_card_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('template')->default('classic');
            $table->string('primary_color')->default('#0a5f47');
            $table->string('secondary_color')->nullable();
            $table->string('header_title')->default('Student Identity Card');
            $table->string('footer_text')->nullable();
            $table->json('show_fields');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_id_card_settings');
    }
};
