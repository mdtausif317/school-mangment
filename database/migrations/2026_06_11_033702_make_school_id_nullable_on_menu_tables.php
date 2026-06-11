<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages_menu_list', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropUnique(['school_id', 'slug']);
        });

        Schema::table('pages_menu_list', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->change();
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();
            $table->unique('slug');
        });

        Schema::table('pages_auth', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('pages_menu_list', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropUnique(['slug']);
        });

        Schema::table('pages_menu_list', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
            $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
            $table->unique(['school_id', 'slug']);
        });

        Schema::table('pages_auth', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable(false)->change();
        });
    }
};
