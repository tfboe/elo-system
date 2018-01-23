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

//TODO don't use this since an error gets thrown anyway when null is returned instead of the wanted value, also remove
//all PhpUnhandledExceptionInspection ignores and make properties private

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