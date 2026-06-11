<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages_auth', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained('pages_menu_list')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('designation_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['menu_id', 'user_id']);
            $table->unique(['menu_id', 'designation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages_auth');
    }
};
