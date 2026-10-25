<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     *
     * Tabel `jobs` menyimpan data loker (lihat docs/ARCHITECTURE.md).
     * Tabel antrean bawaan Laravel dihapus dan QUEUE_CONNECTION=sync
     * karena aplikasi tidak memakai antrean, agar nama `jobs` sesuai SPEC.json.
     */
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('address');
            $table->string('division');
            $table->string('city');
            $table->text('description');
            $table->text('requirements');
            $table->date('deadline')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->boolean('is_active')->default(true);
            $table->foreignId('posted_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'city']);
        });
    }

    /**
     * Balikkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
