<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah jenis pekerjaan dan jumlah lowongan ke tabel `jobs`.
     *
     * `job_type` menyimpan jenis tampilannya (mis. Full-Time, Part-Time) dan
     * `positions_needed` menyimpan berapa banyak lowongan yang dibutuhkan.
     */
    public function up(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('job_type')->nullable();
            $table->unsignedInteger('positions_needed')->default(1);
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['job_type', 'positions_needed']);
        });
    }
};
