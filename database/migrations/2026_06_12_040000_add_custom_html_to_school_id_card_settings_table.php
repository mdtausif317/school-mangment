<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_id_card_settings', function (Blueprint $table) {
            $table->longText('custom_html')->nullable()->after('show_fields');
        });
    }

    public function down(): void
    {
        Schema::table('school_id_card_settings', function (Blueprint $table) {
            $table->dropColumn('custom_html');
        });
    }
};
