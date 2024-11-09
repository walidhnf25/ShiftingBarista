<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJamShift extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jam_shift', function (Blueprint $table) {
            $table->id();
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
            $table->string('id_outlet')->nullable();
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
        Schema::dropIfExists('jam_shift');
    }
}
