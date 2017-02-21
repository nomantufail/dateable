<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('users')->insert([
            'first_name'=>'noman',
            'last_name'=>'tufail',
            'email'=>'noman@gmail.com',
            'gender'=>1,
            'birthday'=>'1993-04-03',
            'access_token'=>'$2a$08$dKWPhcSP5zz3fWHHL1kWEeZI0rFySsQeUGKuYAPq6FUVqkB3YZzoW',
            'password' => '$2a$08$dKWPhcSP5zz3fWHHL1kWEeZI0rFySsQeUGKuYAPq6FUVqkB3YZzoW',
            'fb_id'=>'1234'
        ]);
    }
}
