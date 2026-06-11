<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('subscription_plans')) {
            Schema::create('subscription_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->decimal('price', 10, 2)->default(0);
                $table->unsignedInteger('duration_days')->default(30);
                $table->unsignedInteger('max_users')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('school_subscriptions')) {
            Schema::create('school_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
                $table->dateTime('starts_at');
                $table->dateTime('expires_at');
                $table->string('status')->default('active');
                $table->timestamps();

                $table->index(['school_id', 'status']);
            });
        }

        if (! Schema::hasTable('subscription_payments')) {
            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->cascadeOnDelete();
                $table->foreignId('subscription_plan_id')->constrained()->cascadeOnDelete();
                $table->foreignId('school_subscription_id')->nullable()->constrained('school_subscriptions')->nullOnDelete();
                $table->decimal('amount', 10, 2);
                $table->string('status')->default('pending');
                $table->string('payment_method')->nullable();
                $table->string('payment_reference')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->dateTime('paid_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('school_subscriptions');
        Schema::dropIfExists('subscription_plans');
    }
};
