<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('school_id')->nullable()->after('id')->constrained()->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->after('school_id')->constrained()->nullOnDelete();
            $table->string('user_type')->default('staff')->after('designation_id');
            $table->boolean('is_active')->default(true)->after('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('school_id');
            $table->dropConstrainedForeignId('designation_id');
            $table->dropColumn(['user_type', 'is_active']);
        });
    }
};
