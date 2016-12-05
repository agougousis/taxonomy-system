<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    /**
    * Table name.
    *
    * @var string
    */
    protected $table = 'system_logs';

    /**
    * Auto-update the created_at and updated_at fields
    *
    * @var boolean
    */
    public $timestamps = false;
}
