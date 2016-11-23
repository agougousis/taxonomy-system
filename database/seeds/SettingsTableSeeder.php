<?php

use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the settings seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->truncate();
        DB::table('settings')->insert(['name'=>'rpp', 'value'=>'15', 'last_modified'=>'2016-05-30 12:13:57', 'about'=>'Result items per page.',]);
    }

}