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
        Schema::table('franchaisee_requests', function (Blueprint $table) {
            $table->foreignId('franchaisor_id')->constrained('franchaisors')->after('status')->nullable()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('franchaisee_requests', function (Blueprint $table) {
            $table->dropColumn('franchaisor_id');
        });
    }
};
