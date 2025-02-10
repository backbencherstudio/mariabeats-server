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
        Schema::create('franchaisor_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('franchaisor_id')->constrained('franchaisors')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->string('file_type')->nullable(); // image, video
            $table->string('type')->nullable(); // cover, brief, details1, details2
            $table->timestamps();
        });
    }

    /** 
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('franchaisor_files');
    }
};
