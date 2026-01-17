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
        Schema::create('detail_juals', function (Blueprint $table) {
            $table->id();
            $table->string('no_bon');
            $table->string('kode_barang');
            $table->foreign('kode_barang')->references('kode_barang')->on('barangs')->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('no_bon')->references('no_bon')->on('juals')->onUpdate('cascade')->onDelete('cascade');
            $table->bigInteger('harga');
            $table->bigInteger('jumlah');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_juals');
    }
};
