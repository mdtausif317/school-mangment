<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages_menu_list', function (Blueprint $table) {
            $table->string('route_name')->nullable()->after('slug');
            $table->string('scope')->default('platform')->after('route_name');
        });
    }

    public function down(): void
    {
        Schema::table('pages_menu_list', function (Blueprint $table) {
            $table->dropColumn(['route_name', 'scope']);
        });
    }
};
