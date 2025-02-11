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
        // for franchisor
        Schema::create('franchisor_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('country')->nullable();
            $table->string('subject')->nullable();
            $table->text('message')->nullable();
            $table->string('status')->nullable()->default('pending'); // pending, approved, rejected
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchisor_requests');
    }
};
