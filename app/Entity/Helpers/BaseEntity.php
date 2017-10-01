<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 2:05 AM
 */

namespace App\Entity\Helpers;

use App\Exceptions\ValueNotSet;

/**
 * Class BaseEntity
 * @package App\Entity\Helpers
 */
class BaseEntity
{
  /**
   * Checks if the given method exists
   * @param string $method the method to search
   * @return bool true if it exists and false otherwise
   */
  public function methodExists(string $method)
  {
    return method_exists($this, $method);
  }

  public function ensureNotNull(string $property)
  {
    /** @noinspection PhpVariableVariableInspection */
    if ($this->$property == null) {
      throw new ValueNotSet(static::class, $property);
    }
  }
}