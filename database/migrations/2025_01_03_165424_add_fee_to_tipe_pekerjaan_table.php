<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeeToTipePekerjaanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipe_pekerjaan', function (Blueprint $table) {
            $table->integer('fee')->nullable()->after('tipe_pekerjaan'); // Tambahkan kolom fee setelah kolom tipe_pekerjaan
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
            $table->dropColumn('fee'); // Hapus kolom fee saat rollback
        });
    }
}
