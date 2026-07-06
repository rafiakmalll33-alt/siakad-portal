<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->onDelete('cascade');
            $table->foreignId('mahasiswa_id')->constrained('mahasiswas')->onDelete('cascade');
            $table->integer('pertemuan_ke');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpa'])->default('alpa');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->unique(['kelas_id', 'mahasiswa_id', 'pertemuan_ke']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
