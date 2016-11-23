<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* Domain
*/
class Setting extends Model {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'settings';

  /**
   * Auto-update the created_at and updated_at fields
   *
   * @var boolean
   */
  public $timestamps = false;

}
