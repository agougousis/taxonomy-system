<?php

use App\User;
use App\Models\Sname;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdminRoutesTest extends TestCase
{
    protected $admin;
    protected $non_admin;

    public function setUp(){
        parent::setUp();
        $this->artisan('db:seed',['--class' => 'SnamesTableSeeder']);
        $this->admin = User::where('is_admin',1)->first();
        $this->non_admin = User::where('is_admin',0)->first();
    }

    /**
     * @test
     * @group adminRoutes
     */
    public function create_node(){
        $this->be($this->admin);

        // Add all the required data
        $data = [
            'nodes' =>  [
                [
                    'id'        =>  233111,
                    'parent_id' =>  13,
                    'sname'     =>  'subphylum20',
                    'rank'      =>  'subphylum',
                    'accepted'  =>  1,
                    'authorship'=>  'Tomkens, 1964'
                ]
            ]
        ];

        // Send the request
        $this->call('POST','/names', $data, $cookies = [], $files = [], $server = [], $content = null);
        $this->assertEquals(200,$this->response->getStatusCode(),'Creating a new node as admin user failed!');

        // Check that the new node is in database
        $node = Sname::where('sname','subphylum20')->first();
        $this->assertInstanceOf('App\Models\Sname',$node);
        $this->assertEquals(233111,$node->id);

        // Check that its ancestors has been updated correctly
        $parent = Sname::find(13);
        $this->assertEquals(1,$parent->leaves_num);
        $grantParent = Sname::find(8);
        $this->assertEquals(3,$grantParent->leaves_num);
    }

    /**
     * @test
     * @group adminRoutes
     */
    public function update_node(){
        $this->be($this->admin);

        // Add all the required data
        $data = [
            'nodes' =>  [
                [
                    'id'        =>  14,
                    'sname'     =>  'subphylum14a',
                    'accepted'  =>  1,
                    'authorship'=>  'Tomkens, 1963'
                ]
            ]
        ];

        // Send the request
        $this->call('PUT','/names', $data, $cookies = [], $files = [], $server = [], $content = null);
        $this->assertEquals(200,$this->response->getStatusCode(),'Updating a node as admin user failed!');

        // Check that the node has been updated in database
        $node = Sname::where('sname','subphylum14a')->first();
        $this->assertInstanceOf('App\Models\Sname',$node);
        $this->assertEquals(14,$node->id);
    }

    /**
     * @test
     * @group adminRoutes
     */
    public function delete_node_or_branch(){
        $this->be($this->admin);

        // Send the request
        $this->call('DELETE','/names/16', [], $cookies = [], $files = [], $server = [], $content = null);
        $this->assertEquals(200,$this->response->getStatusCode(),'Deleting a node as admin user failed!');

        // Check that the node is not in database anymore
        $node = Sname::find(16);
        $this->assertEmpty($node);

        // Check that the node's parents has been updated
        $parent = Sname::find(12);
        $this->assertEquals(1,$parent->leaves_num);
        $grandParent = Sname::find(8);
        $this->assertEquals(2,$grandParent->leaves_num);
    }

    /**
     * @test
     * @group adminRoutes
     */
    public function move_node_or_branch(){
        $this->be($this->admin);

        // Add all the required data
        $data = [
            'nodes' =>  [
                [
                    'id'            =>  14,
                    'new_parent_id' =>  13
                ]
            ]
        ];

        // Send the request
        $this->call('PUT','/names/move', $data, $cookies = [], $files = [], $server = [], $content = null);
        $this->assertEquals(200,$this->response->getStatusCode(),'Movind a branch as admin user failed!');

        // Check that the (top branch) node has been moved
        $top = Sname::find(14);
        $this->assertEquals(13,$top->parent_id);

        // Check that the old ancestors has been updated
        $oldParent = Sname::find(12);
        $this->assertEquals(1,$oldParent->leaves_num);
        $oldGrandParent = Sname::find(8);
        $this->assertEquals(2,$oldGrandParent->leaves_num);

        // Check that the new ancestors has been updated
        $newParent = Sname::find(13);
        $this->assertEquals(1,$newParent->leaves_num);

    }

    /**
     * @test
     * @group adminRoutes
     */
    public function node_seeding(){
        $this->be($this->admin);

        // Send the request
        $this->call('POST','/names/12/seeding?how_many_seeds=4', [], $cookies = [], $files = [], $server = [], $content = null);
        $this->assertEquals(200,$this->response->getStatusCode(),'Seeding a node as admin user failed!');

        // Check that nodes were inserted
        $countNode12children = Sname::where('path','LIKE','/8/12%')->count();
        $this->assertEquals(7,$countNode12children);
    }

}