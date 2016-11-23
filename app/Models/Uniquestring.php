<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* Domain
*/
class Uniquestring extends Model {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'uniquestrings';

  /**
   * The primary key
   *
   * @var string
   */
  public $primaryKey = "name";

  /**
   * Auto-update the created_at and updated_at fields
   *
   * @var boolean
   */
  public $timestamps = false;

  /**
   * Don't treat primary key as auto-increment field
   *
   * @var boolean
   */
  public $incrementing = false;

}
