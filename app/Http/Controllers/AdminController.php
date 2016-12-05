<?php

namespace App\Http\Controllers;

use DB;
use Cache;
use Input;
use Validator;
use Illuminate\Support\Facades\Request;
use App\Models\Sname;
use App\Models\Rank;
use App\Models\Uniquestring;

/**
 * Handles administration functionality
 *
 * @author Alexandros Gougousis
 */
class AdminController extends RootController
{
    /**
     * Flushes the application cache
     *
     * @return Response
     */
    public function clearCache()
    {
        Cache::flush();
        return response()->json(['message' => 'Cache was cleared!'])->setStatusCode(200, 'Cache was cleared!');
    }

    /**
     * Creates and adds a number of new nodes in the taxonomy tree
     *
     * @return Response
     */
    public function create()
    {
        // Retrieve nodes array from JSON
        $nodes = Request::get('nodes');
        $node_num = count($nodes);

        // Validate and create the nodes
        $index = 0;
        DB::beginTransaction();

        foreach ($nodes as $node) {
            $addResult = $this->validateAndAdd($node, $index);
            if (!$addResult['success']) {
                DB::rollBack();
                return response()->json([
                    'message' => $addResult['errorMessage'],
                    'errors' => $addResult['errors']
                ])->setStatusCode($addResult['errorCode'], '');
            }

            $index++;
        }

        DB::commit();

        Cache::flush(); // The tree has changed. Clear cache.
        return response()->json(array('message' => $node_num.' node(s) added.'))
                                 ->setStatusCode(200, 'ok');
    }

    /**
     * Validate and insert a POSTed node/name
     *
     * @param array $node
     * @param int $index
     * @return array
     */
    private function validateAndAdd($node, $index)
    {
        $rules = config('validation.sname_create');
        $validator = Validator::make($node, $rules);
        if ($validator->fails()) {
            $validationErrors = $this->getValidationErrorsAsIndexedArray($validator, $index);
            return $this->buildResponseArray(false, $validationErrors, 'Node validation failed', 400);
        }


        // Try to store each node
        $result = $this->addNode($node);

        if (!$result['succeed']) {
            DB::rollBack();
            $errors[] = array(
                'index'     =>  $index,
                'field'     =>  $result['error']['field'],
                'message'   =>  $result['error']['message']
            );

            return $this->buildResponseArray(false, $errors, 'Node creation failed', 400);
        }

        return $this->buildResponseArray(true, [], '', 200);
    }

    /**
     * Returns an array of validation messages including the index
     *
     * @param Illuminate\Validation\Validator $validator
     * @param int $index
     * @return array
     */
    private function getValidationErrorsAsIndexedArray($validator, $index)
    {
        $validationErrors = [];

        foreach ($validator->errors()->getMessages() as $key => $errorMessages) {
            foreach ($errorMessages as $msg) {
                $validationErrors[] = array(
                    'index'     =>  $index,
                    'field'     =>  $key,
                    'message'   =>  $msg
                );
            }
        }

        return $validationErrors;
    }

    /**
     * Returns an array to be used as response by validateAndAdd() method
     *
     * @param boolean $success
     * @param array $errors
     * @param string $errorMessage
     * @param int $errorCode
     * @return array
     */
    private function buildResponseArray($success, $errors, $errorMessage, $errorCode)
    {
        return [
            'success' => $success,
            'errors' => $errors,
            'errorMessage' => $errorMessage,
            'errorCode' => $errorCode
        ];
    }

    /**
     * Updates a number of nodes in the taxonomic tree
     *
     * @return Response
     */
    public function update()
    {
        // Retrieve nodes array from JSON
        $nodes = Request::get('nodes');
        $node_num = count($nodes);

        // Validate the data for each node
        $index = 0;
        DB::beginTransaction();
        foreach ($nodes as $node) {
            $rules = config('validation.sname_update');
            $validator = Validator::make($node, $rules);
            if ($validator->fails()) {
                $validationErrors = $this->getValidationErrorsAsIndexedArray($validator, $index);

                DB::rollBack();
                return response()->json(['message' => 'Node validation failed', 'errors' => $validationErrors])
                                     ->setStatusCode(400, '');
            }

            $record = Sname::find($node['id']);

            if (isset($node['id'])) {
                unset($node['id']);
            }
            if (isset($node['parent_id'])) {
                unset($node['parent_id']);
            }
            if (isset($node['rank'])) {
                unset($node['rank']);
            }

            // Try to store each node
            $record->fill($node);
            $record->save();

            $index++;
        }

        DB::commit();
        return response()->json(array('message' => $node_num.' node(s) updated.'))
                                 ->setStatusCode(200, 'ok');
    }

    /**
     * Moves a numder of nodes to a new parent
     *
     * @param int $nid
     * @return Response
     */
    public function move()
    {
        // Retrieve nodes array from JSON
        $moves = Request::get('nodes');
        $moves_num = count($moves);

        // Validate the data for each node
        $index = 0;
        DB::beginTransaction();
        foreach ($moves as $move) {
            // Validate the
            $rules = config('validation.sname_move');
            $validator = Validator::make($move, $rules);
            if ($validator->fails()) {
                $validationErrors = $this->getValidationErrorsAsIndexedArray($validator, $index);

                DB::rollBack();
                return response()->json(['message' => 'Node validation failed', 'errors' => $validationErrors])
                                     ->setStatusCode(400, '');
            }

            // Check if the move is legal
            // (if the node was to move the only thing that would change is the parent_id)
            $new_parent_id = $move['new_parent_id'];
            $new_parent = Sname::find($new_parent_id)->toArray();
            $nodeObj = Sname::find($move['id']);

            $validityResult = $this->isLegalMove($nodeObj, $new_parent);
            if (!$validityResult['success']) {
                DB::rollBack();
                return response()->json([
                    'message' => $validityResult['errorMessage'],
                    'errors' => $validityResult['errors']
                ])->setStatusCode($validityResult['errorCode'], '');
            }

            // Everything OK. We can move on.

            $old_parent_id = $nodeObj->parent_id;
            $departingLeaves = $nodeObj->leaves_num;

            $this->updateExAncestors($old_parent_id, $departingLeaves);

            // Move the top node
            $nodeObj->parent_id = $new_parent_id;
            $nodeObj->path = $new_parent['path'].'/'.$new_parent_id;
            $nodeObj->save();

            $this->updateNewAncestors($new_parent_id, $departingLeaves);
            $this->updateDescendantsPath($nodeObj);

            $index++;
        }

        DB::commit();
        Cache::flush(); // The tree has changed. Clear cache.
        return response()->json(array('message' => $moves_num.' node(s) moved.'))
                                 ->setStatusCode(200, 'ok');
    }

    /**
     * Checks if a node move is valid, as far as ranks are concerned
     *
     * @param Sname $nodeObj
     * @param array $new_parent
     * @return array
     */
    private function isLegalMove(Sname $nodeObj, $new_parent)
    {
        $node = [];
        $node['parent_id'] = $new_parent['id'];
        $node['rank'] = $nodeObj->rank;
        $position_status = $this->validateNodeRank($node, $new_parent);

        if (!$position_status['valid']) {
            $errors = [];
            $errors[] = array(
                'index'     =>  $index,
                'field'     =>  $position_status['error_field'],
                'message'   =>  $position_status['error_message']
            );
            return $this->buildResponseArray(false, $errors, 'Node validation failed', 400);
        }

        return $this->buildResponseArray(true, [], '', 200);
    }

    /**
     * Updates the path of branch nodes after a branch move
     *
     * @param Sname $nodeObj
     */
    private function updateDescendantsPath(Sname $nodeObj)
    {
        $old_path = $nodeObj->path;
        $old_descendant_path = $old_path.'/'.$nodeObj->id;
        $new_descendant_path = $nodeObj->path.'/'.$nodeObj->id;
        Sname::changePaths($old_descendant_path, $new_descendant_path);
    }

    /**
     * Update node/branch ex-ancestors after a node/branch move
     *
     * @param int $old_parent_id
     * @param int $departingLeaves
     */
    private function updateExAncestors($old_parent_id, $departingLeaves)
    {
        $oldParent = Sname::find($old_parent_id);
        $oldParent->leaves_num -= $departingLeaves;
        $oldParent->save();

        if ($oldParent->leaves_num == 0) {
            $grandDepartingLeaves = $departingLeaves-1;
        } else {
            $grandDepartingLeaves = $departingLeaves;
        }

        $grandAncestors = $oldParent->getAncestors();
        foreach ($grandAncestors as $ancestor) {
            $ancestor->leaves_num -= $departingLeaves;
            $ancestor->save();
        }
    }

    /**
     * Update the node/branch new ancestors after a node/branch move
     *
     * @param int $new_parent_id
     * @param int $departingLeaves
     */
    private function updateNewAncestors($new_parent_id, $departingLeaves)
    {
        $newParent = Sname::find($new_parent_id);
        $arrivingLeaves = $departingLeaves;

        if ($newParent->leaves_num > 0) {
            $grandArrivingLeaves = $arrivingLeaves;
        } else {
            $grandArrivingLeaves = $arrivingLeaves-1;
        }

        $newParent->leaves_num += $arrivingLeaves;
        $newParent->save();

        $grandAncestors = $newParent->getAncestors();
        foreach ($grandAncestors as $ancestor) {
            $ancestor->leaves_num += $grandArrivingLeaves;
            $ancestor->save();
        }
    }

    /**
     * Deletes a tree branch
     *
     * Deletes a node alongside all the nodes that are its descendants
     *
     * @param int $nid
     * @return Response
     */
    public function delete($nid)
    {
        // Check if a node with ID equal to $nid exists
        $node = Sname::find($nid);
        if (empty($node)) {
            return response()->json(['message' => 'Invalid name ID'])->setStatusCode(400, 'Invalid name ID');
        }

        // Save some information before the deletion
        $parent_id = $node->parent_id;
        $removedLeaves = max(1, $node->leaves_num);

        // Delete node (and its descendants)
        $result = $node->deleteSubtree();
        if ($result['success']) {
            $this->updateExAncestors($parent_id, $removedLeaves);

            Cache::flush(); // The tree has changed. Clear cache.
            return response()->json(array())->setStatusCode(200, '');
        } else {
            return response()->json(array())->setStatusCode(500, $result['message']);
        }
    }

    /**
     * Displays the administration (tree management) page
     *
     * @return View
     */
    public function managePage()
    {
        $roots = Sname::getRoots(); // $roots is a Collection
        $treeRoots = $roots->map([$this, 'transformToTreeNode']);

        $jsonTree = json_encode($treeRoots);

        $ranks = flatten(Rank::select('title')->orderBy('order')->get()->toArray());
        $rank_titles = array();
        foreach ($ranks as $rank) {
            $rank_titles[$rank] = $rank;
        }

        return view('web.manage')
                ->with('ranks', $rank_titles)
                ->with('jsonTree', $jsonTree);
    }

    /**
     * Adds a number of dummy nodes under a certain node
     *
     * Each time a new node is inserted, the required modifications are made to the tree.
     * The location of the new node is selected randomly alongside the branch of the tree
     * that starts from the specified node.
     *
     * @param int $node_id
     * @return String/JSON
     */
    public function nodeSeeding($node_id)
    {
        // Check if a node with ID equal to $nid exists
        $root_node = Sname::find($node_id);
        if (empty($root_node)) {
            return response()->json(['message' => 'Invalid name ID', 'errors' => array()])->setStatusCode(400, '');
        }

        // Check that the number of dummy nodes has been provided
        if (!Input::has('how_many_seeds')) {
            return response()->json(['message' => 'Number of seeds is not defined!', 'errors' => array()])->setStatusCode(400, '');
        }

        // Check that the number of dummy nodes is a positive integer
        $how_many = Input::get('how_many_seeds');
        if ((!is_numeric($how_many))||($how_many <= 0)) {
            return response()->json(['message' => 'Number of seeds is not a positive integer!', 'errors' => array()])->setStatusCode(400, '');
        }

        $result = $this->addBranchChildren($how_many, $node_id);
        if ($result['status'] == 'failure') {
            return response()->json(['message' => $result['message'], 'errors' => array()])->setStatusCode(500, '');
        }

        Cache::flush(); // The tree has been modified. Flush the cache!
        return response()->json(array('message' => $how_many.' node(s) added.'))->setStatusCode(200, 'ok');
    }

    /**
     * Used when seeding with the node_seeding() method.
     *
     * Handles the main job of seeding the tree with dummy nodes
     *
     * @param int $how_many
     * @param int $root_node_id
     */
    private function addBranchChildren($how_many, $root_node_id)
    {
        // We need to clear the table in order to define a new scope of random string uniqueness.
        DB::table('uniquestrings')->truncate();

        // We need to use IDs that have not been used, so we start from the
        // maximum ID that have already been used.
        $result = DB::select('select max(id) as maxid from snames');
        $this->max_id = $result[0]->maxid;

        DB::beginTransaction();
        for ($i= 1; $i<=$how_many; $i++) {
            try {
                $new_id = ++$this->max_id;

                // Select random parent
                // The random parent should be under the specified node
                $root_node = Sname::find($root_node_id);
                $parent = $root_node->getRandomDescendant();

                // In case the root node has no children
                if (empty($parent)) {
                    $parent = $root_node;
                }

                // Select child rank
                $nextRank = Rank::where('directParent', $parent->rank)->first();
                if (!empty($nextRank)) {
                    // Build the new node
                    $node = new Sname();
                    $node->fill([
                        'id'        =>  $new_id,
                        'parent_id' =>  $parent->id,
                        'path'      =>  $parent->path.'/'.$parent->id,
                        'sname'     =>  $this->randomString(25),
                        'rank'      =>  $this->selectChildRank($parent, $nextRank),
                        'accepted'   =>  1
                    ]);
                    // Add the node to the tree
                    $node->save();

                    $this->updateAncestorsAfterInsertion($parent, $node);
                } else {
                    $this->max_id--;
                    $i--;
                    continue;
                }
            } catch (Exception $ex) {
                DB::rollback();
                return [
                    'status' => 'failure',
                    'message'=> $ex->getMessage()
                ];
            }
        }

        DB::commit();
        return [
            'status' => 'success',
            'message'=> ''
        ];
    }

    /**
     * Selects the rank of a new child according to tree rules (used in seeding)
     *
     * @param Sname $parent
     * @param Rank $nextRank
     * @return string
     */
    private function selectChildRank(Sname $parent, Rank $nextRank)
    {
        // Get information about the parent rank
        $parentRankInfo = Rank::where('title', $parent->rank)->first();

        switch ($nextRank->title) {
            case "Variety":
                $childRank = "Variety";
                break;
            case "Form":
                $childRank = "Form";
                break;
            default:
                if ($parentRankInfo->isMainRank == 1) {
                    $nextMainRank = Rank::where('isMainRank', 1)->where('mainParent', $parent->rank)->first();
                } else {
                    $nextMainRank = Rank::where('isMainRank', 1)->where('mainParent', $parentRankInfo->mainParent)->first();
                }

                if ($nextRank->title == $nextMainRank->title) {
                    $childRank = $nextRank->title;
                } else {
                    $validRanks = [$nextRank->title,$nextMainRank->title];
                    $childRank = $validRanks[array_rand($validRanks)];
                }
        }

        return $childRank;
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
    protected function randomString($length = 32)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        do {
            $rString = '';
            for ($i = 0; $i < $length; $i++) {
                $rString .= $characters[rand(0, strlen($characters) - 1)];
            }
            $code = Uniquestring::find($rString);
            if (empty($$code)) {
                $uniqueString = true;
            } else {
                $uniqueString = false;
            }
        } while (!$uniqueString);

        $uq = new Uniquestring();
        $uq->name = $rString;
        $uq->save();

        return $rString;
    }
}
