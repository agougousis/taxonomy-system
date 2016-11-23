<?php

use Illuminate\Database\Seeder;

class RanksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('ranks')->truncate();
        DB::table('ranks')->insert(['title'=>'Kingdom',   'order'=>1,     'isMainRank'=>1,]);
        DB::table('ranks')->insert(['title'=>'Phylum',    'order'=>2,     'isMainRank'=>1,'mainParent'=>'Kingdom',  'directParent'=>'Kingdom']);
        DB::table('ranks')->insert(['title'=>'Subphylum', 'order'=>2.25,  'isMainRank'=>0,'mainParent'=>'Phylum',   'directParent'=>'Phylum']);
        DB::table('ranks')->insert(['title'=>'Infraphylum','order'=>2.5,  'isMainRank'=>0,'mainParent'=>'Phylum',   'directParent'=>'Subphylum']);
        DB::table('ranks')->insert(['title'=>'Superclass','order'=>2.75,  'isMainRank'=>0,'mainParent'=>'Phylum',   'directParent'=>'Infraphylum']);
        DB::table('ranks')->insert(['title'=>'Class',     'order'=>3,     'isMainRank'=>1,'mainParent'=>'Phylum',   'directParent'=>'Superclass']);
        DB::table('ranks')->insert(['title'=>'Subclass',  'order'=>3.25,  'isMainRank'=>0,'mainParent'=>'Class',    'directParent'=>'Class']);
        DB::table('ranks')->insert(['title'=>'Infraclass','order'=>3.5,   'isMainRank'=>0,'mainParent'=>'Class',    'directParent'=>'Subclass']);
        DB::table('ranks')->insert(['title'=>'Superorder','order'=>3.75,  'isMainRank'=>0,'mainParent'=>'Class',    'directParent'=>'Infraclass']);
        DB::table('ranks')->insert(['title'=>'Order',     'order'=>4,     'isMainRank'=>1,'mainParent'=>'Class',    'directParent'=>'Superorder']);
        DB::table('ranks')->insert(['title'=>'SubOrder',  'order'=>4.25,  'isMainRank'=>0,'mainParent'=>'Order',    'directParent'=>'Order']);
        DB::table('ranks')->insert(['title'=>'Infraorder','order'=>4.5,   'isMainRank'=>0,'mainParent'=>'Order',    'directParent'=>'SubOrder']);
        DB::table('ranks')->insert(['title'=>'Superfamily','order'=>4.75, 'isMainRank'=>0,'mainParent'=>'Order',    'directParent'=>'Infraorder']);
        DB::table('ranks')->insert(['title'=>'Family',    'order'=>5,     'isMainRank'=>1,'mainParent'=>'Order',    'directParent'=>'Superfamily']);
        DB::table('ranks')->insert(['title'=>'Subfamily', 'order'=>5.25,  'isMainRank'=>0,'mainParent'=>'Family',   'directParent'=>'Family']);
        DB::table('ranks')->insert(['title'=>'Tribe',     'order'=>5.5,   'isMainRank'=>0,'mainParent'=>'Family',   'directParent'=>'Subfamily']);
        DB::table('ranks')->insert(['title'=>'Subtribe',  'order'=>5.75,  'isMainRank'=>0,'mainParent'=>'Family',   'directParent'=>'Tribe']);
        DB::table('ranks')->insert(['title'=>'Genus',     'order'=>6,     'isMainRank'=>1,'mainParent'=>'Family',   'directParent'=>'Subtribe']);
        DB::table('ranks')->insert(['title'=>'Subgenus',  'order'=>6.5,   'isMainRank'=>0,'mainParent'=>'Genus',    'directParent'=>'Genus']);
        DB::table('ranks')->insert(['title'=>'Species',   'order'=>7,     'isMainRank'=>1,'mainParent'=>'Genus',    'directParent'=>'Subgenus']);
        DB::table('ranks')->insert(['title'=>'Variety',   'order'=>7.3,   'isMainRank'=>0,'mainParent'=>'Species',  'directParent'=>'Species']);
        DB::table('ranks')->insert(['title'=>'Form',      'order'=>7.6,   'isMainRank'=>0,'mainParent'=>'Species',  'directParent'=>'Variety']);

    }
}