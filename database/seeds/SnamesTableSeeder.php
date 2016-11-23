<?php

use Illuminate\Database\Seeder;
use App\Models\Rank;
use App\Models\Sname;
use App\Models\RawSname;
use App\Models\Uniquestring;

class SnamesTableSeeder extends Seeder
{

    private $row_counter = 0;
    private $ranks = array();

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        DB::table('snames')->truncate();

        // Load the ranks table locally (faster execution in massive importation)
        $rankList = Rank::all();
        foreach($rankList as $rank){
            $this->ranks[$rank->title] = $rank->toArray();
        }

        $this->add_node(['id'=>1,'parent_id'=>null,'sname' => 'Animalia', 'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>1,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>2,'parent_id'=>null,'sname' => 'Archaea',  'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>2,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>3,'parent_id'=>null,'sname' => 'Bacteria', 'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>3,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>4,'parent_id'=>null,'sname' => 'Chromista','rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>4,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>5,'parent_id'=>null,'sname' => 'Fungi',    'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>5,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>6,'parent_id'=>null,'sname' => 'Plantae',  'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>6,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>7,'parent_id'=>null,'sname' => 'Protozoa', 'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>7,'authorship'=>'noworms, 1603']);
        $this->add_node(['id'=>8,'parent_id'=>null,'sname' => 'Viruses',  'rank'=>'Kingdom','accepted'=>1,'related_to_accepted'=>8,'authorship'=>'noworms, 1603']);

        /*********************
         * Seeding manually  *
         *********************/

        $this->add_node(['id'=>9,'parent_id'=>3,'path'=>'/3','sname' => 'phylum9',  'rank'=>'Phylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Jeknins, 1733']);
        $this->add_node(['id'=>10,'parent_id'=>4,'path'=>'/4','sname' => 'phylum10',  'rank'=>'Phylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Tomkins, 1814']);
        $this->add_node(['id'=>11,'parent_id'=>5,'path'=>'/5','sname' => 'phylum11',  'rank'=>'Phylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Maunhaim, 1753']);
        $this->add_node(['id'=>12,'parent_id'=>8,'path'=>'/8','sname' => 'phylum12',  'rank'=>'Phylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Robinshon, 1877']);
        $this->add_node(['id'=>13,'parent_id'=>8,'path'=>'/8','sname' => 'phylum13',  'rank'=>'Phylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Anderson, 1793']);
        $this->add_node(['id'=>14,'parent_id'=>12,'path'=>'/8/12','sname' => 'subphylum14',  'rank'=>'Subphylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Anderson, 1798']);
        $this->add_node(['id'=>15,'parent_id'=>14,'path'=>'/8/12/14','sname' => 'class15',  'rank'=>'Class','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Bing, 1902']);
        $this->add_node(['id'=>16,'parent_id'=>12,'path'=>'/8/12','sname' => 'class16',  'rank'=>'Class','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Bing, 1912']);
        $this->add_node(['id'=>17,'parent_id'=>3,'path'=>'/3','sname' => 'phylum17',  'rank'=>'Phylum','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Bing, 1902']);
        $this->add_node(['id'=>18,'parent_id'=>9,'path'=>'/3/9','sname' => 'class18',  'rank'=>'Class','accepted'=>1,'related_to_accepted'=>null,'authorship'=>'Geller, 1934']);
        $this->add_node(['id'=>19,'parent_id'=>9,'path'=>'/3/9','sname' => 'class19',  'rank'=>'Class','accepted'=>0,'related_to_accepted'=>18,'authorship'=>'Geller, 1934']);

        /***************************************
         * Seeding automatically and randomly  *
         ***************************************/
        //$this->row_counter = 8;
        //$time_start = microtime_float();
        //$this->addRandomChildren(20);
        //$time_end = microtime_float();
        //$time = $time_end - $time_start;
        //echo "time = ".$time;
    }

    /**
     * Adds a fixed number of random nodes in the tree
     *
     * @param int $max_names
     */
    private function addRandomChildren($max_names){

        for($i= 1; $i<=$max_names; $i++){

            try {
                $this->row_counter++;

                // Select random parent
                $randomRows = DB::select('select * from snames order by RAND() LIMIT 1');
                $parentRow = $randomRows[0];
                $parent = Sname::find($parentRow->id);

                // Get information about the parent rank
                $parentRankInfo = Rank::where('title',$parent->rank)->first();
                //echo "\n parent rank = ".$parentRankInfo->title;

                // Select child rank
                $nextRank = Rank::where('directParent',$parent->rank)->first();
                if(!empty($nextRank)){

                    switch($nextRank->title){
                        case "Variety":
                            $childRank = "Variety";
                            break;
                        case "Form":
                            $childRank = "Form";
                            break;
                        default:
                            if($parentRankInfo->isMainRank == 1){
                                $nextMainRank = Rank::where('isMainRank',1)->where('mainParent',$parent->rank)->first();
                            } else {
                                $nextMainRank = Rank::where('isMainRank',1)->where('mainParent',$parentRankInfo->mainParent)->first();
                            }
                            if($nextRank->title == $nextMainRank->title){
                                $childRank = $nextRank->title;
                            } else {
                                $choice = rand(1,2);
                                if($choice == 1){
                                    $childRank = $nextRank->title;
                                } else {
                                    $childRank = $nextMainRank->title;
                                }
                            }
                    }
                } else {
                    $this->row_counter--;
                    continue;
                }

                // Add node
                $node = [
                    'id'        =>  $this->row_counter,
                    'parent_id' =>  $parent->id,
                    'sname'     =>  $this->randomString(25),
                    'rank'      =>  $childRank,
                    'accepted'  =>  1,
                    'authorship'=>  str_random(20)
                    ];
                $this->add_node($node);


            } catch (Exception $ex) {
                echo "<pre>";
                echo "\n Exception: ".$ex->getMessage();
                echo "\n Child: id= ".$this->row_counter." , parent_id = ".$parent->id." , rank = ".$childRank." , parent rank = ".$parent->rank." , nextRenk->title = ".$nextRank->title;
                die();
            }

        }

    }

    /**
     * Generates a unique string.
     *
     * The uniqueness of the string should be defined inside the scope of an action.
     * For that reason, we need to clear the relevant table before we start this action.
     * This methos is mainly used while seeding.
     *
     * @param type $length
     * @return string
     */
    protected function randomString($length = 32) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $rString = '';
            for ($i = 0; $i < $length; $i++) {
                $rString .= $characters[rand(0, strlen($characters) - 1)];
            }
            $code = Uniquestring::find($rString);
            if(empty($$code))
                $uniqueString = true;
            else
                $uniqueString = false;
        } while(!$uniqueString);

        return $rString;
    }

    /**
     * Adds a node to the tree
     *
     * @param array $node
     * @return array
     */
    protected function add_node($node){

        unset($node['children']); // In case of batch execution

        // If there is a parent, we'd better retrieve it now. We will need it in
        // this function and there is no reason to retrieve it again inside the
        // validate_node_rank() function. We will pass it as a parameter.
        if(!empty($node['parent_id'])){
            $parent_node = Sname::where('id',$node['parent_id'])->first();
            if(empty($parent_node)){
                return array('succeed'=>false,'error'=>array('field'=>'parent_id','message'=>'Node with Id = '.$node['id'].' and parent_id = '.$node['parent_id'].' could not be added! Parent not in DB!'));
            }
        } else {
            $parent_node = null;
        }

        // Store the new name
        try {
            // Create the child as root node
            $child = new Sname($node);
            $child->save();

            if(!empty($node['parent_id'])){
                // Fix path
                $child->path = $parent_node->path.'/'.$parent_node->id;
                $child->save();

                // If the parent node already had children, then its leaves
                // and its ancestors' leaves increase by one
                if($parent_node->leaves_num > 0){
                    $ancestors = $child->getAncestors();
                    foreach($ancestors as $ancestor){
                        $ancestor->leaves_num++;
                        $ancestor->save();
                    }
                } else {
                    // Increase only the parents'children by one
                    $parent_node->leaves_num++;
                    $parent_node->save();
                }
            }

            return array('succeed'=>true,'message'=>"");
        } catch (Exception $ex) {
            return array('succeed'=>false,'error'=>array('field'=>'-','message'=>$ex->getMessage()));
        }
    }

    /**
     * Validates that the rank of a node to be inserted is compatible with
     * the rank of its parent node
     *
     * The validation takes place taking into account the following rules:
     *
     * (a) if the new name/node belongs to main rank A, then its parent can
     *     be:
     *      - a name with the right previous main rank A
     *      - a name with a subrank between A and B
     * (b) if the new name/node belongs to the first subrank of main rank A, then
     *     its parent can be:
     *      - the main rank A
     * (c) if the new name/node belongs to the second or lower subrank of main
     *     rank A, then its parent can be:
     *      - the immediate previous subrank of A
     *
     * @param array $node
     * @param Sname $parent_node
     * @return array
     */
    protected function validate_node_rank($node,$parent_node=null){
        $valid = true;
        $error_field = "";
        $error_message = "";

        // The node's rank is valid according it's the parent node rank
        // (the ranks table is already in memory, so we don't need to
        // make a pile of queries in case of massive importation)
        //
        // There should be a parent_id. You cannot add Kingdoms manually.
        if(!empty($node['parent_id'])){
            $mainParentRank = $this->ranks[$node['rank']]['mainParent'];
            $directParentRank = $this->ranks[$node['rank']]['directParent'];

            if(!(($this->ranks[$parent_node->rank]['order'] <= $this->ranks[$directParentRank]['order'])&&($this->ranks[$parent_node->rank]['order'] >= $this->ranks[$mainParentRank]['order']))){
                $valid = false;
                $error_field = "rank";
                $error_message = "A node with '".$node['rank']."' rank can have as parent only a node with rank between '".$directParentRank."' and '".$mainParentRank."'.";
            }
        }

        return array(
            'valid' =>  $valid,
            'error_field'   =>  $error_field,
            'error_message' =>  $error_message
        );

    }

}



