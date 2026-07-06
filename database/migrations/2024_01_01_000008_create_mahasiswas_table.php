<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nim')->unique();
            $table->foreignId('ukt_id')->constrained('ukts')->onDelete('cascade');
            $table->foreignId('dosen_wali_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status_kuliah', ['aktif', 'nonaktif', 'cuti', 'lulus'])->default('aktif');
            $table->decimal('ipk', 3, 2)->default(0);
            $table->integer('semester_saat_ini')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswas');
    }
};
