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
        Schema::table('jobs', function (Blueprint $table) {
            $table->date('opens_at')->nullable()->after('deadline');
            $table->date('closes_at')->nullable()->after('opens_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('closes_at');
        });

        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('opens_at');
        });
    }
};
