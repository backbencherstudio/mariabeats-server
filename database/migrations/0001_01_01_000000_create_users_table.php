<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('approved_at')->nullable()->default(null);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->nullable()->default('user'); // admin, user

            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->string('preferred_location')->nullable();
            $table->decimal('investment', 10, 2)->nullable();
            $table->string('timeframe')->nullable()->default('1 year'); // 1 year, 2 years, 3 years, 4 years, 5 years, 6 years, 7 years, 8 years, 9 years, 10 years
            $table->string('joined_at')->nullable();
            $table->string('end_at')->nullable(); // minimum 1 year
            $table->string('status')->nullable()->default('active'); // active, inactive

            // Franchaisor id when user is user/franchaisor
            $table->foreignId('franchaisor_id')->nullable()->constrained('franchaisors')->onDelete('cascade');

            $table->rememberToken(); 
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
