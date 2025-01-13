<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFeeAndBatasToTipePekerjaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipe_pekerjaan', function (Blueprint $table) {
            $table->integer('min_fee')->nullable(); // Tambahkan kolom min_fee
            $table->integer('avg_fee')->nullable(); // Tambahkan kolom avg_fee
            $table->integer('max_fee')->nullable(); // Tambahkan kolom max_fee
            $table->integer('pendapatan_batas_bawah')->nullable(); // Tambahkan kolom pendapatan_batas_bawah
            $table->integer('pendapatan_batas_atas')->nullable(); // Tambahkan kolom pendapatan_batas_atas
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipe_pekerjaan', function (Blueprint $table) {
            $table->dropColumn([
                'min_fee',
                'avg_fee',
                'max_fee',
                'pendapatan_batas_bawah',
                'pendapatan_batas_atas',
            ]); // Hapus kolom-kolom yang ditambahkan
        });
    }
}
