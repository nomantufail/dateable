<?php

use Illuminate\Database\Seeder;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('user_interests')->insert([
            'user_id' => 1,
            'age_min'=>18,
            'age_max'=>30,
            'gender' => 2
        ]);
    }
}
