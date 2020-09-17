<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insertOrIgnore(
            [
                [
                    'name' => 'John',
                    'email' => 'john@gmail.com',
                    'salary' => 100,
                ],
                [
                    'name' => 'Mike',
                    'email' => 'mike@gmail.com',
                    'salary' => 200,
                ],
                [
                    'name' => 'Simon',
                    'email' => 'simon@gmail.com',
                    'salary' => 300,
                ],
                [
                    'name' => 'Kevin',
                    'email' => 'kevin@gmail.com',
                    'salary' => 400,
                ],

            ]
        );
    }
}
