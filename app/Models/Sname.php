<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;

class Sname extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'snames';

    /**
     * Allow these fields to be massively assigned
     *
     * @var array
     */
    protected $fillable = array('id', 'parent_id', 'path', 'rank', 'sname', 'authorship', 'accepted', 'related_to_accepted');

    /**
     * Exempt these fields when converting the model to array or json
     *
     * @var array
     */
    protected $hidden = ['created_at', 'updated_at'];

    /**
     * Returns the number of tree leaves under this node/name
     *
     * @return int
     */
    public function countLeaves()
    {
        if (empty($this->path)) {
            $pathStarter = "/".$this->id;
        } else {
            $pathStarter = $this->path."/".$this->id;
        }
        // We are counting the leaves ignoring the synonyms (not accepted names) in the tree
        $query = DB::select("select count(*) as leavesNum from (select * from snames where (accepted = 1) and (path LIKE '".$pathStarter."/%' OR path = '".$pathStarter."')) nodes where not exists (select * from snames where accepted = 1 and parent_id = nodes.id);");
        $obj = $query[0];
        return $obj->leavesNum;
    }

    /**
     * Returns a list of all the ancestors of this node/name
     *
     * @return \SplObjectStorage
     */
    public function getAncestors()
    {
        $node = $this;
        $ancestors = new \SplObjectStorage();
        while (!empty($node->parent_id)) {
            $node = Sname::find($node->parent_id);
            $ancestors->attach($node);
        }

        return $ancestors;
    }

    /**
     * Returns an array with all the IDs of this node's ancestors
     *
     * @return array
     */
    public function getAncestorsIds()
    {
        $node = $this;
        $ids = array();
        while (!empty($node->parent_id)) {
            $node = Sname::find($node->parent_id);
            $ids[] = $node->id;
        }

        // We reverse the lineage order because when we want to locate a node
        // in the tree, we expand the parent and then the child.
        return array_reverse($ids);
    }

    /**
     * Returns all the root nodes
     *
     * @return Illuminate\Database\Collection
     */
    public static function getRoots()
    {
        return Sname::where('parent_id', null)->get();
    }

    /**
     * Returns all the root nodes
     *
     * @return array Array of stdClass items
     */
    public static function getRootsArray()
    {
        return DB::table('snames')->where('parent_id', null)->get();
    }

    /**
     * Returns the number of this node's children
     *
     * @return int
     */
    public function countChildren()
    {
        return Sname::where('parent_id', $this->id)->count();
    }

    /**
     * Returns the number of this node's children that are accepted names
     *
     * @return int
     */
    public function countAcceptedChildren()
    {
        return Sname::where('parent_id', $this->id)
                ->where('accepted', 1)
                ->count();
    }

    /**
     * Returns the children of this node
     *
     * @return Illuminate\Database\Collection
     */
    public function getChildren()
    {
        return Sname::where('parent_id', $this->id)->get();
    }

    /**
     * Returns the accepted children of this node
     *
     * @return Illuminate\Database\Collection
     */
    public function getAcceptedChildren()
    {
        return Sname::select('id', 'sname', 'rank', 'authorship', 'leaves_num')
                ->where('parent_id', $this->id)
                ->where('accepted', 1)
                ->get();
    }

    /**
     * Returns a random descendant of this node
     *
     * @return Sname
     */
    public function getRandomDescendant()
    {
        return Sname::where('path','like',$this->path.'/'.$this->id.'%')
                ->orderByRaw("RAND()")
                ->limit(1)
                ->first();
    }

    /**
     * Change a path prefix to all nodes
     *
     * It is called after a branch/node move
     *
     * @param string $old_anc_path
     * @param string $new_anc_path
     */
    public static function change_paths($old_anc_path, $new_anc_path)
    {
        $nodes = Sname::where('path','like', $old_anc_path.'%')->get();
        foreach ($nodes as $node) {
            $pos = strpos($node->path, $old_anc_path);
            if (!($pos === false)) {
                $new_path = substr_replace($node->path, $new_anc_path, $pos, strlen($old_anc_path));
            }
            $node->path = $new_path;
            $node->save();
        }
    }

    /**
     * Remove this node and all its descendants from the tree
     *
     * @return array
     */
    public function deleteSubtree()
    {
        DB::beginTransaction();
        try {
            $countLeaves = $this->leaves_num;
            if (!empty($this->parent_id)) {
                $ancestors = $this->getAncestors();
                foreach ($ancestors as $ancestor) {
                    $ancestor->leaves_num = $ancestor->leaves_num - $countLeaves;
                    $ancestor->save();
                }
            }

            $sub_path = $this->path;
            Sname::where('path', 'like', $sub_path.'%')->delete();
        } catch (Exception $ex) {
            DB::rollBack();
            return array('success' => false, 'message' => $ex->getMessage());
        }

        DB::commit();
        return array('success' => true, 'message' => '');
    }
}
