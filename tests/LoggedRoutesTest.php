<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LoggedRoutesTest extends TestCase
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
     * @group loggedRoutes
     */
    public function get_tree_roots(){

        $this->be($this->non_admin);

        // Call the API and get the response
        $this->call('GET','tree_roots');
        $this->assertEquals(200,$this->response->getStatusCode(),'Get tree roots as logged user failed!');
        $roots = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertEquals(8,count($roots));
        $this->assertObjectHasAttributes(['label','id','rank','leaves'],$roots[0]);

    }

    /**
     * @test
     * @group loggedRoutes
     */
    public function get_accepted_children_as_called_by_jqtree(){
        $this->be($this->admin);

        // Mock Cache facade
        Cache::shouldReceive('has')->andReturn(false);
        Cache::shouldReceive('put')->andReturn(true);

        // Call the API and get the response
        $this->call('GET','node_children?node=8');
        $this->assertEquals(200,$this->response->getStatusCode(),'Failed to get node children,as logged user, like jqtree does!');
        $children = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertEquals(2,count($children));
        $this->assertObjectHasAttributes(['label','id','rank','leaves'],$children[0]);
    }

    /**
     * @test
     * @group loggedRoutes
     */
    public function get_all_children_as_called_by_jqtree(){
        $this->be($this->non_admin);

        // Mock Cache facade
        Cache::shouldReceive('has')->andReturn(false);
        Cache::shouldReceive('put')->andReturn(true);

        // Call the API and get the response
        $this->call('GET','all_node_children?node=8');
        $this->assertEquals(200,$this->response->getStatusCode(),'Failed to get all node children,as logged user, like jqtree does!');
        $children = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertEquals(2,count($children));
        $this->assertObjectHasAttributes(['label','id','rank','accepted','leaves'],$children[0]);
    }

}