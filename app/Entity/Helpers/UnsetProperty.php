<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/19/17
 * Time: 5:46 PM
 */

namespace App\Entity\Helpers;


use App\Exceptions\ValueNotSet;

/**
 * Trait UnsetProperty
 * @package App\Entity\Helpers
 */
trait UnsetProperty
{
//<editor-fold desc="Public Methods">
  /**
   * Ensures that the given property is not null and throws an exception otherwise
   * @param string $property the property name to check for null
   * @throws ValueNotSet if the value of the property is null
   */
  public function ensureNotNull(string $property)
  {
    /** @noinspection PhpVariableVariableInspection */
    if (!isset($this->$property)) {
      throw new ValueNotSet(static::class, $property);
    }
  }
//</editor-fold desc="Public Methods">
}