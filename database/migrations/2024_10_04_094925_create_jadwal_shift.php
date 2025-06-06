<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJadwalShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jadwal_shift', function (Blueprint $table) {
            $table->id();
            $table->string('id_jam')->nullable();
            $table->string('id_tipe_pekerjaan')->nullable();
            $table->string('id_outlet')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('id_user')->nullable();
            $table->string('status')->nullable();
            $table->time('check_in_time')->nullable();
            $table->time('check_out_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jadwal_shift');
    }
}
