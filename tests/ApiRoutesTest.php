<?php

use App\User;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApiRoutesTest extends TestCase
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
     * @group publicApi
     */
    public function search_without_mode()
    {
        // Call the API and get the response
        $this->call('GET','api/names/');
        $this->assertEquals(200,$this->response->getStatusCode(),'Searching for names as visitor failed!');
        $response = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertInstanceOf('stdClass',$response);
        $this->assertObjectHasAttribute('data',$response);
        $this->assertEquals(15,count($response->data));
        $this->assertEquals(1,count($response->hasMore));

        // Validate the structure of a node/name item
        $names = $response->data;
        $this->assertEquals(4, (new ArrayObject($names[0]))->count());
        $this->assertObjectHasAttribute('id',$names[0]);
        $this->assertObjectHasAttribute('sname',$names[0]);
        $this->assertObjectHasAttribute('rank',$names[0]);
        $this->assertObjectHasAttribute('authorship',$names[0]);
    }

    /**
     * @test
     * @group publicApi
     */
    public function search_with_roots_mode()
    {
        // Call the API and get the response
        $this->call('GET','api/names?mode=roots');
        $this->assertEquals(200,$this->response->getStatusCode(),'Searching for root names as visitor failed!');
        $response = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertInstanceOf('stdClass',$response);
        $this->assertObjectHasAttribute('data',$response);
        $roots = $response->data;
        $this->assertEquals(8,count($roots));

        // Validate the structure of each root item
        $this->assertEquals(4, (new ArrayObject($roots[0]))->count());
        $this->assertObjectHasAttributes(['id','label','rank','leaves'], $roots[0]);

        // Check that the returned items are all Kingdoms
        $root_ranks = array_unique(array_map(function($root){
            return $root->rank;
        },$roots));
        $this->assertEquals(1,count($root_ranks));
        $this->assertEquals('Kingdom',$root_ranks[0]);
    }

    /**
     * @test
     * @group publicApi
     */
    public function search_with_invalid_mode(){
        // Call the API and get the response
        $this->call('GET','api/names?mode=recent');
        $this->assertEquals(400,$this->response->getStatusCode(),'Searching for names, as visitor, using an invalid mode succeeded!');
    }

    /**
     * @test
     * @group publicApi
     */
    public function read_specific_name(){
        // Call the API and get the response
        $this->call('GET','api/names/14');
        $this->assertEquals(200,$this->response->getStatusCode(),'Reading a specific name as visitor failed!');
        $name = json_decode($this->response->getContent());

        // Check the structure of the response
        $this->assertEquals(42, (new ArrayObject($name))->count());
        $this->assertObjectHasAttributes([
            'id','parent_id','path','leaves_num','sname','uninomen',
            'rank','accepted','related_to_accepted','sortnophyl','basionym',
            'FKaphiaBasionym','protonym','sortnospe','authorship','authonym',
            'nothonym','prefavatar','fk_ref_morphonym','year','fk_telangio_taxon',
            'fk_getangio_taxon','grouptax','phylum','remarks','comnames','comnames_languages',
            'fk_ref_comnames','taxonp','taxongp','fk_eunis_morphonym','fk_aphia_morphonym',
            'fk_aphia_ergonym','fk_aphia_parent','checked_by','checked_date',
            'validated_by','validated_date','workfield','status_synonymy','status_onym',
            'status_chresonym'
            ], $name);

        // Check the content of the response
        $this->assertEquals(14,$name->id);
        $this->assertEquals(12,$name->parent_id);
        $this->assertEquals('/8/12',$name->path);
        $this->assertEquals(1,$name->leaves_num);
        $this->assertEquals('subphylum14',$name->sname);
        $this->assertEquals(1,$name->accepted);
    }

    /**
     * @test
     * @group publicApi
     */
    public function attempt_read_not_existing_name(){
        // Call the API and get the response
        $this->call('GET','api/names/66');
        $this->assertEquals(400,$this->response->getStatusCode(),'Reading an name that does not exist succeeded!');
    }

    /**
     * @test
     * @group publicApi
     */
    public function get_node_children_tree_information(){
        // Call the API and get the response
        $this->call('GET','api/names/14/children');
        $this->assertEquals(200,$this->response->getStatusCode(),"Getting node's children as visitor failed!");
        $response = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertInstanceOf('stdClass',$response);
        $this->assertObjectHasAttribute('data',$response);
        $children = $response->data;
        $this->assertEquals(1,count($children));

        // Validate content of the response
        $this->assertEquals('class15',$children[0]->sname);
        $this->assertEquals(15,$children[0]->id);
        $this->assertEquals('Class',$children[0]->rank);
    }

    /**
     * @test
     * @group publicApi
     */
    public function get_node_ancestors(){
        // Call the API and get the response
        $this->call('GET','api/names/14/ancestors');
        $this->assertEquals(200,$this->response->getStatusCode(),"Getting node's ancestors as visitor failed!");
        $ancestorsArray = json_decode($this->response->getContent());

        // Validate the response structure
        $this->assertEquals(2,count($ancestorsArray));

        // Validate the response content
        $this->assertArraySubset([8,12],$ancestorsArray);
    }

    /**
     * @test
     * @group publicApi
     */
    public function get_node_synonyms(){
        // Call the API and get the response
        $this->call('GET','api/names/18/synonyms');
        $this->assertEquals(200,$this->response->getStatusCode(),"Getting node's synonyms as visitor failed!");
        $response = json_decode($this->response->getContent());

        // Validate the general structure of the response
        $this->assertInstanceOf('stdClass',$response);
        $this->assertObjectHasAttribute('data',$response);
        $synonyms = $response->data;
        $this->assertEquals(1,count($synonyms));

        // Validate the response content
        $this->assertEquals(19,$synonyms[0]->id);
        $this->assertEquals(0,$synonyms[0]->accepted);
        $this->assertEquals(18,$synonyms[0]->related_to_accepted);
        $this->assertEquals(0,$synonyms[0]->leaves_num);
    }

}