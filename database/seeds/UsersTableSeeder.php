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
        DB::table('users')->truncate();

        DB::table('users')->insert([
            'id'        =>  1,
            'email'     =>  'user1@gmail.com',
            'password'  =>  bcrypt('user1pwd'),
            'firstname' =>  'Alexandros',
            'lastname'  =>  'Gougousis',
            'is_admin'  =>  1
        ]);
        DB::table('users')->insert([
            'id'        =>  2,
            'email'     =>  'user2@gmail.com',
            'password'  =>  bcrypt('user2pwd'),
            'firstname' =>  'Nikos',
            'lastname'  =>  'Loukoulos',
            'is_admin'  =>  0
        ]);
        DB::table('users')->insert([
            'id'        =>  3,
            'email'     =>  'user3@gmail.com',
            'password'  =>  bcrypt('user3pwd'),
            'firstname' =>  'Tonia',
            'lastname'  =>  'Papadaki',
            'is_admin'  =>  0
        ]);

    }
}
