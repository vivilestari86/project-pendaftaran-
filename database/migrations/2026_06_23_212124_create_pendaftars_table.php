<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftars', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('id_pendaftar')->unique();
            $table->string('profesi')->nullable();
            $table->string('foto')->nullable();
            $table->enum('wilayah', ['Jawa Barat', 'Jawa Timur', 'Jawa Tengah', 'Lainnya'])->default('Lainnya');
            $table->enum('status_kelengkapan', ['lengkap', 'belum_lengkap'])->default('belum_lengkap');
            $table->string('email')->nullable();
            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->timestamps();
            $table->index(['status_kelengkapan']);
            $table->index(['wilayah']);
            $table->index(['tanggal_daftar']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftars');
    }
};