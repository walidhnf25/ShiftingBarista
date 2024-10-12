<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPermissionsToPermissions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Menambahkan data ke tabel permissions
        DB::table('permissions')->insert([
            ['name' => 'view-tipepekerjaan', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'view-jamshift', 'guard_name' => 'web', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
