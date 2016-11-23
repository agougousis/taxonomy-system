<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(App::environment() === 'production'){
            exit('No seeding for production environment!');
        }

        Model::unguard();

        // Empty all the tables before seeding
        $tables = ['uniquestrings','system_logs']; 
        foreach($tables as $table){
            DB::table($table)->truncate();
        }

        $this->call(UsersTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(RanksTableSeeder::class);
        $this->call(SnamesTableSeeder::class);

        Model::reguard();
    }
}
