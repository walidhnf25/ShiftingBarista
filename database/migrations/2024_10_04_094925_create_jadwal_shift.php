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
            $table->string('id_jadwal', 3)->nullable();
            $table->string('id_outlet', 3)->nullable();
            $table->string('id_user', 3)->nullable();
            $table->boolean('acc_manager')->nullable();
            $table->string('status', 20)->nullable();
            $table->date('hari/tanggal')->nullable();
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
