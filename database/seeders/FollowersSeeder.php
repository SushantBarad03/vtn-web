<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class FollowersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('followers')->insert([
            'followers' => \Str::random(1),
            'user_id' => '1',
        ]);
    }
}
