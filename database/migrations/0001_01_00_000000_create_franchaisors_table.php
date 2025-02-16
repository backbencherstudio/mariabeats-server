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
        Schema::create('franchaisors', function (Blueprint $table) {
            $table->id();
            $table->string('logo_path')->nullable();
            $table->string('brand_name')->nullable();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('industry')->nullable();
            $table->dateTime('joined_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->string('timeframe')->nullable();
            // in brief
            $table->string('brief_heading')->nullable();
            $table->text('brief_description')->nullable();
            $table->string('brief_country_of_region')->nullable();
            $table->string('brief_available')->nullable();
            $table->string('brief_business_type')->nullable();
            $table->string('brief_min_investment')->nullable();
            // details    
            $table->string('details1_heading')->nullable();
            $table->text('details1_description')->nullable();
            $table->string('details2_heading')->nullable();
            $table->text('details2_description')->nullable();

            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchaisors');
    }
};
