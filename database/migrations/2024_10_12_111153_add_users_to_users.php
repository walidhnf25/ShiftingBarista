<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUsersToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Data to be inserted
        $users = [
            [
                'name' => 'Manager',
                'email' => 'manager@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('123456'), // Hash the password
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make('123456'), // Hash the password
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            // Check if the user already exists based on the email
            $exists = DB::table('users')->where('email', $user['email'])->exists();

            // Insert only if the user does not exist
            if (!$exists) {
                DB::table('users')->insert($user);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
