<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages_buttons_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('button_id')->constrained('pages_buttons')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['button_id', 'user_id']);
            $table->unique(['button_id', 'designation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages_buttons_auth');
    }
};
