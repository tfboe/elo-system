<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:05 AM
 */

namespace App\Entity\Helpers;


/**
 * Class BaseEntity
 * @package App\Entity\Helpers
 */
abstract class BaseEntity
{

//<editor-fold desc="Public Methods">

  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method): bool
  {
    return method_exists($this, $method);
  }
//</editor-fold desc="Public Methods">
}