<?php

namespace App\Http\Controllers;

use DB;
use Excel;
use Cache;
use Auth;
use Redirect;
use Validator;
use App\Models\Sname;
use Illuminate\Support\Facades\Request;

/**
 * Handles functionality of logged in users or visitors
 *
 * @author Alexandros Gougousis
 */
class WebController extends RootController
{

    /**
     * Used when importing names from CSV. It is being filled up with the
     * contents of the CSV for easier data manipulation.
     *
     * @var array
     */
    private $batchNodes = array();

    /**
     * Used as a counter when importing names from CSV.
     *
     * @var int
     */
    private $batchIndex = 0;

    /**
     * Used when importing names from CSV.
     *
     * @var array
     */
    private $batchErrors = array();

    /**
     * The landing page for site visitors
     *
     * @return View
     */
    public function index()
    {
        if (Auth::check()) {
            return Redirect::to('/home');
        } else {
            return view('welcome');
        }
    }

    /**
     * Displays the Home pahe
     *
     * @return View
     */
    public function home()
    {
        return view('web.home')->with('is_admin', Auth::user()->is_admin);
    }

    /**
     * Displays the API documentation page
     *
     * @return Response
     */
    public function documentation()
    {
        return view('api_documentation');
    }

    /**
     * Returns information about the tree roots
     *
     * The information returned, is used to initialize the tree UI
     *
     * @return Response
     */
    public function treeRoots()
    {
        $roots = Sname::getRoots(); // $roots is a Collection
        $treeRoots = $roots->map([$this, 'transformToTreeNode']);

        return response()->json($treeRoots);
    }

    /**
     * Inserts tree nodes from CSV to the database
     *
     * The nodes are inserted in the same order they are in the CSV.
     * It is an alternative to the load_depth_first() method.
     *
     * @return Response
     */
    public function loadAndRebuild()
    {
        // Check if we are building a tree from scratch
        $clear = Request::get('clear');
        if (!empty($clear) && ($clear == 'yes')) {
            // Clear the table
            DB::table('snames')->truncate();
        }

        // Check that a valid file has been sent
        $rules = config('validation.add_nodes_from_file');
        $form = Request::all();
        $validator = Validator::make($form, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Save the uploaded file (temporarily)
        if (!$this->moveUploadedFile()) {
            return back()->withInput('file_error', 'Something went wrong! Please try again in a few minutes.');
        }

        DB::beginTransaction();

        try {
            $this->loadCsvInMemory($filepath);

            /********************************************
             *  Add each node (CSV line) into the tree  *
             ********************************************/
            foreach ($this->batchNodes as $node) {
                // Validate node (for the moment we don't require that the parent
                // node exists in the database but, if not, we are going to
                // check if it is going to be imported from this CSV).
                $rules = config('validation.rebuild_sname_create');
                $validator = Validator::make($node, $rules);
                if ($validator->fails()) {
                    $errors = $validator->errors()->getMessages();
                    foreach ($errors as $key => $value) {
                        $this->batchErrors[] = array(
                            'index'     =>  $node['id'],
                            'field'     =>  $key,
                            'message'   =>  $value[0]   // only the first message for that key
                        );
                    }
                    DB::rollBack();
                    return back()->with('importation_errors', $this->batchErrors);
                }

                if (!empty($node['parent_id'])) {
                    // Check that the parent node exists in database or it is
                    // going to be imported from CSV
                    $parentInDb = (empty(Sname::find($node['parent_id']))) ? false : true;
                    $parentInCsv = isset($this->batchNodes[$node['parent_id']]);
                    if (!($parentInDb||$parentInCsv)) {
                        DB::rollBack();
                        $errors = [
                            array(
                                'index'     =>  $node['id'],
                                'field'     =>  'parent_id',
                                'message'   =>  "This node's parent does exist in database nor it is going to be imported from this CSV."
                            )
                        ];
                        return back()->with('importation_errors', $errors);
                    }

                    // Check that the new node's rank is compatible with its
                    // position in the tree
                    $parent_node = $this->batchNodes[$node['parent_id']];
                    $position_status = $this->validateNodeRank($node, $parent_node);
                    if (!$position_status['valid']) {
                        $this->batchErrors[] = array(
                            'index'     =>  $node['id'],
                            'field'     =>  $position_status['error_field'],
                            'message'   =>  $position_status['error_message']
                        );
                        DB::rollBack();
                        $this->logEvent('Node validation failed!'.  json_encode($this->batchErrors), 'info');
                        return back()->with('importation_errors', $this->batchErrors);
                    }
                } else {
                    // The node should be of Kingdom rank
                }

                // Save the node
                $this->add_node($node);
            }
        } catch (Exception $ex) {
            $this->batchErrors[] = array(
                'index'     =>  $node['id'],
                'field'     =>  '-',
                'message'   =>  $ex->getMessage()
            );
            DB::rollBack();
            return back()->with('importation_errors', $this->batchErrors);
        }

        // Write all database changes
        DB::commit();
        return back();
    }

    /**
     * Returns information about a node's children
     *
     * It is being called through AJAX when unfolding a tree node
     *
     * @return JSON
     */
    public function nodeChildren()
    {
        $node_id = Request::get('node');

        if (!config('cache.disable_caching')) {
            $key = 'node_children_'.$node_id;
            if (Cache::has($key)) {
                return Cache::get($key);
            }
        }

        $parent = Sname::find($node_id);
        $children = $parent->getAcceptedChildren();
        $jsonTree = $children->map([$this,'transformToTreeNode']);

        if (!config('cache.disable_caching')) {
            Cache::put($key, json_encode($jsonTree), $this->caching_period);
        }

        return response()->json($jsonTree);
    }

    /**
     * Returns information about a node's children including not accepted names
     *
     * It is being called through AJAX when unfolding a tree node. It is used
     * exclusively in the management page. The administrator should be able to
     * see these names in the tree in order to edit them or delete them.
     *
     * @return JSON
     */
    public function allNodeChildren()
    {
        $node_id = Request::get('node');

        if (!config('cache.disable_caching')) {
            $key = 'node_children_'.$node_id;
            if (Cache::has($key)) {
                return response()->json(json_decode(Cache::get($key)));
            }
        }

        $parent = Sname::find($node_id);
        $children = $parent->getChildren();
        $jsonTree = $children->map([$this,'transformToTreeNodeWithAccepted']);

        if (!config('cache.disable_caching')) {
            Cache::put($key, json_encode($jsonTree), $this->caching_period);
        }

        return response()->json($jsonTree);
    }

    /**
     * Inserts tree nodes from CSV to the database
     *
     * It is an alternative to the load_and_rebuild() method
     *
     * @return Response
     */
    public function loadDepthFirst()
    {
        // Check if we are building a tree from scratch
        $clear = Request::get('clear');
        if (!empty($clear) && ($clear == 'yes')) {
            // Clear the table
            DB::table('snames')->truncate();
        }

        // Check that a valid file has been sent
        $rules = config('validation.add_nodes_from_file');
        $form = Request::all();
        $validator = Validator::make($form, $rules);
        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Save the uploaded file (temporarily)
        $fileName = Request::file('csv_file')->getClientOriginalName();
        $destinationPath = base_path().'/resources/uploaded';
        $filepath = $destinationPath."/".$fileName;
        if (!$this->moveUploadedFile($destinationPath, $filepath)) {
            return back()->withInput('file_error', 'Something went wrong! Please try again in a few minutes.');
        }

        DB::beginTransaction();

        $this->batchErrors = array();
        $this->batchIndex = 0;
        $this->batchNodes = array();    // CSV loaded in memory as table (key = name id, value = CSV row)
        $root_ids = array();            // Contains the IDs of top nodes (if the CSV contains only full trees,
                                        // all these nodes should be of 'Kingdom', otherwise they can be just
                                        // the top node of a branch)

        try {
            $this->loadCsvInMemory($filePath);
            $root_ids = $this->buildChildrenColumn();

            /**************************************************
             *  Build the tree in a pre-order depth-first way *
             **************************************************/
            foreach ($root_ids as $root_id) {
                if (!$this->addTreeNode($root_id)) {
                    DB::rollBack();
                    return back()->with('importation_errors', $this->batchErrors);
                }
            }
        } catch (Exception $ex) {
            $this->batchErrors[] = array(
                'index'     =>  $lineArray['id'],
                'field'     =>  '-',
                'message'   =>  $ex->getMessage()
            );

            return back()->with('importation_errors', $this->batchErrors);
        }

        // Write all database changes
        DB::commit();
        return back();
    }

    /**
     * Copy the uploaded file to the designated directory
     *
     * @return boolean
     */
    private function moveUploadedFile($destinationPath, $fileName)
    {
        try {
            Request::file('csv_file')->move($destinationPath, $fileName);
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * Loads the CSV file in a memory array
     *
     * @param string $filePath
     */
    private function loadCsvInMemory($filePath)
    {
        $lines = Excel::load($filePath)->get();
        foreach ($lines as $line) {   // for each node (each Excel line is a Maatwebsite\Excel\Collections\CellCollection object)
            $lineArray = $line->all();
            $this->batchNodes[$lineArray['id']] = $lineArray;
        }
    }

    /**
     * Adds a new column named 'children' in the batchNodes memory array
     *
     * This column contains the IDs of the node's children.
     */
    private function buildChildrenColumn()
    {
        foreach ($this->batchNodes as $node) {
            if (!empty($node['parent_id'])) {
                $parent_id = $node['parent_id'];

                // Check if node's parent is also defined in the CSV
                if (empty($this->batchNodes[$parent_id])) {
                    // If not, this node is the top of a branch which should be
                    // attached to a tree that already exists in the database
                    $root_ids[] = $node['id'];
                } else {
                    // If exists, it should be added after its parent, so
                    // add it as a children of its parent
                    if (empty($this->batchNodes[$parent_id]['children'])) {
                        $this->batchNodes[$parent_id]['children'] = ''+$node['id'];
                    } else {
                        $this->batchNodes[$parent_id]['children'] = implode(',', array($this->batchNodes[$parent_id]['children'], $node['id']));
                    }
                }
            } else {
                $root_ids[] = $node['id']; // Keep a list of root nodes
            }
        }

        return $root_ids;
    }

    /**
     * Adds a node and its children from CSV memory table in the tree
     *
     * It is called by load_depth_first() and works recursively
     *
     * @param int $node_id
     * @return boolean
     */
    private function addTreeNode($node_id)
    {
        $node = $this->batchNodes[$node_id];

        // Check node rank validity
        if (!$this->isValidBatchNode($node)) {
            return false;
        }

        // Add the node to the tree
        $result = $this->addNode($node);

        if (!$result['succeed']) {
            $this->batchErrors[] = array(
                'index'     =>  $node['id'],
                'field'     =>  $result['error']['field'],
                'message'   =>  $result['error']['message']
            );

            return false;
        }

        // Add node's children in the tree
        if (!empty($node['children'])) {
            $children_ids = explode(',', $node['children']);
            foreach ($children_ids as $child_id) {
                if (!$this->addTreeNode($child_id)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Checks the rank validity of a batch (CSV) node
     *
     * @param array $node
     * @return boolean
     */
    private function isValidBatchNode($node)
    {
        // If this is not a Kingdom, check that the new node's rank
        // is compatible with its position in the tree
        if (!empty($node['parent_id'])) {
            $parent_node = $this->batchNodes[$node['parent_id']];
            $position_status = $this->validateNodeRank($node, $parent_node);
            return $position_status['valid'];
        }
        return true;
    }
}
