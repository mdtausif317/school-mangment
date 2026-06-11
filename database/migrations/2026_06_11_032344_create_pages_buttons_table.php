<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('pages_menu_list')->cascadeOnDelete();
            $table->string('button_title');
            $table->string('button_link');
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages_buttons');
    }
};
