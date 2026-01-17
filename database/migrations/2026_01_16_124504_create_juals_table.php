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
        Schema::create('juals', function (Blueprint $table) {
            $table->id();
            $table->string('no_bon')->unique();
            $table->bigInteger('total');
            $table->integer('diskon');
            $table->bigInteger('bayar');
            $table->bigInteger('kembali');
            $table->string('kode_kasir');
            $table->foreign('kode_kasir')->references('kode_kasir')->on('kasirs')->onUpdate('cascade')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('waktu');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juals');
    }
};
