<?php

namespace App\Http\Controllers;

use App\Models\Sname;
use App\Models\Setting;
use Illuminate\Support\Facades\Input;

/**
 * Implements the public API endpoints
 *
 * @author Alexandros Gougousis
 */
class ApiController extends RootController
{
    /**
     * Retrieves the complete lineage of a node starting from the root.
     *
     * @param int $nid
     * @return Response
     */
    public function ancestors($nid)
    {
        // Check if a node with ID equal to $nid exists
        $node = Sname::find($nid);
        if (empty($node)) {
            return response()->json([
                'message'=>'Invalid name ID',
                'errors' => array()
                ])->setStatusCode(400, '');
        }

        $ancestorsIds = $node->getAncestorsIds();
        return response()->json($ancestorsIds);
    }

    /**
     * Returns information about a specific node
     *
     * @param int $nid
     * @return Response
     */
    public function read($nid)
    {
        // Check if a node with ID equal to $nid exists
        $node = Sname::find($nid);
        if (empty($node)) {
            return response()->json([
                'message'=>'Invalid name ID',
                'errors' => array()
                ])->setStatusCode(400, '');
        }

        // Send back the node info
        return response()->json($node)->setStatusCode(200, '');
    }

    /**
     * Returns a list of all names (with paging) or just the root nodes
     *
     * @return Response
     */
    public function listing()
    {
        $mode = Input::get('mode') ?: "search";

        switch($mode){
            case 'roots':
                // ---- Return root nodes -----
                $roots = Sname::getRoots(); // $roots is a Collection
                $treeRoots = $roots->map([$this, 'transformRoot']);
                return response()->json(['data' => $treeRoots]);
                break;
            case 'search':
                $searchResults = $this->searchWithPagination();
                return response()->json(array(
                    'data'      => $searchResults['name_list'],
                    'hasMore'   => $searchResults['has_more']
                    ));
                break;
            default:
                return response()->json(['message' => 'Invalid search mode', 'errors' => array()])
                                     ->setStatusCode(400, '');
                break;

        }
    }

    /**
     * Builds the search query
     *
     * @return array
     */
    protected function searchWithPagination()
    {
        $rpp_setting = Setting::where('name', 'rpp')->first();
        $rpp = $rpp_setting->value;
        $page = Input::get('page')?: 1;
        $take = $rpp+1; // We need one more to figure out if there are more results
        $skip = ($page-1)*$rpp;

        // We display only accepted names
        $query = Sname::select('id', 'sname', 'rank', 'authorship')->where('accepted', 1);

        // Search by ?
        if (Input::has('sname')) {
            $sname = Input::get('sname');
            $query->where('sname', 'like', "%$sname%");
        }

        if (Input::has('authorship')) {
            $authorship = Input::get('authorship');
            $query->where('authorship', 'like', "%$authorship%");
        }

        $name_list_plus_one = $query->skip($skip)->take($take)->get();
        $has_more = (count($name_list_plus_one) > $rpp) ? 1 : 0;
        // Now that we figured out if there another page of results
        // let's get rid of the extra name from the page results
        $name_list = $name_list_plus_one->take($take-1);

        return ['name_list' => $name_list, 'has_more' => $has_more];
    }

    /**
     * Converts an Sname root node to an appropriate tree object
     *
     * @param Sname $root
     * @return \stdClass
     */
    public function transformRoot(Sname $root)
    {
        $rootObj = new \stdClass();
        $rootObj->label = $root->sname." (".$root->countLeaves().")";
        $rootObj->id = $root->id;
        $rootObj->rank = $root->rank;
        return $this->countChildrenLeaves($rootObj, $root);
    }

    /**
     * Returns all the synonyms of a node
     *
     * A synonym is technically any node that has a 'related_to_accepted' value
     * equal to the provided node ID and an 'accepted' value equal to 0.
     *
     * @param int $nid
     * @return Response
     */
    public function synonyms($nid)
    {
        // Check if a node with ID equal to $nid exists
        $node = Sname::find($nid);
        if (empty($node)) {
            return response()->json(['message' => 'Invalid name ID', 'errors' => array()])
                                     ->setStatusCode(400, '');
        }

        // Check if node corresponds to an accepted name
        if ($node->accepted == 0) {
            return response()->json(['message' => 'This is not an accepted name', 'errors' => array()])
                                     ->setStatusCode(400, '');
        }

        $synonyms = Sname::where('accepted', 0)->where('related_to_accepted', $nid)->get();
        return response()->json(['data' => $synonyms]);
    }

    /**
     * Returns the direct children of a node.
     *
     * Information about the number of their children and the number of leaves
     * for each child is added.
     *
     * @param int $nid
     * @return Response
     */
    public function children($nid)
    {
        // Check if a node with ID equal to $nid exists
        $parent = Sname::find($nid);
        if (empty($parent)) {
            return response()->json(['message' => 'Invalid name ID', 'errors' => array()])
                                     ->setStatusCode(400, '');
        }

        $children = $parent->getAcceptedChildren();
        return response()->json(['data' => $children]);
    }

    /**
     * Adds the number of leaves to a node object
     *
     * @param stdClass $phpNode The stdClass node where the number of leaves will be added
     * @param Sname $baumNode The Sname node that will be used to calculate the number of leaves
     * @return stdClass
     */
    private function countChildrenLeaves($phpNode, $baumNode)
    {
        $children = $baumNode->getChildren();
        if ($children->count() > 0) {
            $phpNode->leaves = $children->count();
        } else {
            $phpNode->leaves = 0;
        }

        return $phpNode;
    }
}
