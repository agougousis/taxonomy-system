<?php

use App\User;
use App\Models\Setting;
use App\Models\Sname;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DatabaseHealthTest extends TestCase
{
    protected $admin;
    protected $non_admin;

    public function setUp(){
        parent::setUp();
        $this->admin = User::where('is_admin',1)->first();
        $this->non_admin = User::where('is_admin',0)->first();
    }

    /**
     * @test
     * @group dbHealth
     */
    public function required_settings_exist_in_database(){
        $rppResults = Setting::where('name','rpp')->get();
        $this->assertEquals(1,count($rppResults));
    }

}