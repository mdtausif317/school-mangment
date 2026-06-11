<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages_menu_list', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('pages_menu_list')->nullOnDelete();
            $table->string('title');
            $table->string('slug');
            $table->string('icon')->default('fas fa-circle');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('display')->default(false);
            $table->timestamps();

            $table->unique(['school_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages_menu_list');
    }
};
