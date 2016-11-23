<?php

namespace App\Http\Controllers;

use App\Models\Sname;
use App\Models\Rank;
use App\Models\SystemLog;

/**
 * A controller that implements features common to most controllers.
 *
 * @author   Alexandros Gougousis
 */
class RootController extends Controller
{
    /**
     * The contents of 'ranks' table
     *
     * @var array
     */
    private $ranks = array();

    /**
     * How long queries are cached (in min)
     *
     * @var string
     */
    protected $caching_period;

    public function __construct()
    {
        $this->caching_period = config('cache.default_caching_period');

        // Load the ranks table locally (faster execution in massive importation)
        $rankList = Rank::all();
        foreach ($rankList as $rank) {
            $this->ranks[$rank->title] = $rank->toArray();
        }
    }

    /*
     * Logs an event
     *
     * @param string $message
     * @param string $category
     */
    protected function log_event($message, $category)
    {
        if (Auth::check()) {
            $user_id = Auth::user()->email;
        } else {
            $user_id = 'unknown'; // system, visitor or open/api route
        }

        $action = app('request')->route()->getAction();
        $controller = class_basename($action['controller']);
        list($controller, $action) = explode('@', $controller);

	$log = new SystemLog();
	$log->when 	=   date("Y-m-d H:i:s");
	$log->controller =  $controller;
	$log->method 	=   $action;
	$log->message 	=   $message;
        $log->category  =   $category;
        $log->actor     =   $user_id;
	$log->save();
    }

    /**
     * Adds a node to the tree
     *
     * @param array $node
     * @return array
     */
    protected function add_node($node)
    {
        unset($node['children']); // In case of batch execution

        // Store the new name
        try {
            // Create the child as root node
            $child = new Sname($node);
            $child->save();

            if (!empty($node['parent_id'])) {
                $parent_node = Sname::where('id', $node['parent_id'])->first();

                // Check that the parent exists
                if (empty($parent_node)) {
                    return array('succeed' => false, 'error' => array('field' => 'parent_id', 'message' => 'Node with Id = '.$node['id'].' and parent_id = '.$node['parent_id'].' could not be added! Parent not in DB!'));
                }

                // Fix path
                $child->path = $parent_node->path.'/'.$parent_node->id;
                $child->save();

                $this->updateAncestorsAfterInsertion($parent_node, $child);
            }

            return array('succeed' => true, 'message' => "");
        } catch (Exception $ex) {
            return array('succeed' => false, 'error' => array('field' => '-', 'message' => $ex->getMessage()));
        }
    }

    /**
     * Update the number of leaves in the ancestors of a new tree node
     *
     * @param Sname $parent
     * @param Sname $child
     */
    protected function updateAncestorsAfterInsertion(Sname $parent, Sname $child)
    {
        // If the parent node already had children, then its leaves
        // and its ancestors' leaves increase by one
        if ($parent->leaves_num > 0) {
            $ancestors = $child->getAncestors();
            foreach($ancestors as $ancestor){
                $ancestor->leaves_num++;
                $ancestor->save();
            }
        } else {
            // Increase only the parents'children by one
            $parent->leaves_num++;
            $parent->save();
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
     * @param array $parent_node
     * @return array
     */
    protected function validate_node_rank($node, $parent_node = null)
    {
        $valid = true;
        $error_field = "";
        $error_message = "";

        // The node's rank is valid according it's the parent node rank
        // (the ranks table is already in memory, so we don't need to
        // make a pile of queries in case of massive importation)
        //
        // There should be a parent_id. You cannot add Kingdoms manually.
        if (!empty($node['parent_id'])) {
            $mainParentRank = $this->ranks[$node['rank']]['mainParent'];
            $directParentRank = $this->ranks[$node['rank']]['directParent'];

            if (!(($this->ranks[$parent_node['rank']]['order'] <= $this->ranks[$directParentRank]['order'])&&($this->ranks[$parent_node['rank']]['order'] >= $this->ranks[$mainParentRank]['order']))) {
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

    /**
     * Converts an Sname node to an appropriate tree object
     *
     * @param Sname $node
     * @param boolean $withAccepted (optional)
     * @return \stdClass
     */
    public function transformToTreeNode(Sname $node, $withAccepted = false)
    {
        $treeNode = new \stdClass();

        $treeNode->id = $node->id;
        $treeNode->rank = $node->rank;
        $treeNode->label = $node->sname." (".$node->countLeaves().")";

        $count_children = $node->getChildren()->count();
        if($count_children){
            $treeNode->load_on_demand = true;
            $treeNode->leaves = $count_children;
        } else {
            $treeNode->leaves = 0;
        }

        // This property will be asked in case we are transforming both
        // accepted and non-accepted nodes
        if($withAccepted){
            $treeNode->accepted = $node->accepted;
        }

        return $treeNode;
    }

    /**
     * A decoration for transformToTreeNode
     *
     * Since transformToTreeNode() is usually passed as Callable argument
     * to Collection.map() method, we need a way to set its second parameter
     *
     * @param Sname $node
     * @return \stdClass
     */
    public function transformToTreeNodeWithAccepted(Sname $node)
    {
        return $this->transformToTreeNode($node, true);
    }

}
