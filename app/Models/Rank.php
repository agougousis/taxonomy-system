<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
* Domain
*/
class Rank extends Model {

  /**
   * Table name.
   *
   * @var string
   */
  protected $table = 'ranks';

  /**
   * Table primary key
   *
   * @var string
   */
  public $primaryKey = "title";

  /**
   * Don't treat primary key as auto-increment field
   *
   * @var boolean
   */
  public $incrementing = false;

}
