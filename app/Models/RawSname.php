<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* Domain
*/
class RawSname extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'snames';

    /**
     * Properties guarded from massive assignment
     *
     * @var array
     */
    protected $guarded = array('lft', 'rgt', 'depth');

    /**
     * Don't treat primary key as auto-increment field
     *
     * @var boolean
     */
    public $incrementing = false;
}
