<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/18/17
 * Time: 10:24 PM
 */

namespace App\Entity\Categories;

use App\Exceptions\ValueNotValid;

/**
 * Class BasicEnum
 * @package App\Entity\Categories
 */
abstract class BasicEnum
{
//<editor-fold desc="Fields">
  /** @var null|mixed[][] */
  private static $constCacheArray = NULL;

  /** @var null|string[][] */
  private static $constCacheArrayCaseMapping = NULL;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * Ensures that the given value is valid by throwing an exception if it is not valid
   * @param mixed $value the value to check for validity
   * @param bool $strict if yes type checks are performed
   * @throws ValueNotValid if the value is not valid
   */
  public static function ensureValidValue($value, bool $strict = true): void
  {
    if (!self::isValidValue($value, $strict)) {
      throw new ValueNotValid($value, get_called_class());
    }
  }

  /**
   * Gets a list of all names in this enum
   * @return mixed[]
   */
  public static function getNames(): array
  {
    return array_keys(self::getConstants());
  }

  /**
   * Gets the value corresponding to the given name
   * @param string $name the name for which to get the value
   * @param bool $strict if yes retrieval is done case sensitive and otherwise case insensitive
   * @return mixed the corresponding value
   * @throws ValueNotValid if the name is not valid
   */
  public static function getValue(string $name, bool $strict = false)
  {
    $constants = self::getConstants();
    if ($strict) {
      if (array_key_exists($name, $constants)) {
        return $constants[$name];
      }
    } else {
      $mapping = self::getCaseMapping();
      $key = strtolower($name);
      if (array_key_exists($key, $mapping)) {
        return $constants[$mapping[$key]];
      }
    }

    throw new ValueNotValid($name, get_called_class(), "getNames");
  }

  /**
   * Gets a list of all values in this enum
   * @return mixed[]
   */
  public static function getValues(): array
  {
    return array_values(self::getConstants());
  }

  /**
   * Checks if a given name is part of this enum
   * @param string $name the name to check for validity
   * @param bool $strict if yes check is done case sensitive and otherwise case insensitive
   * @return bool true if the name is part of the enum and false otherwise
   */
  public static function isValidName(string $name, bool $strict = false): bool
  {
    $constants = self::getConstants();

    if ($strict) {
      return array_key_exists($name, $constants);
    }

    $keys = array_map('strtolower', array_keys($constants));
    return in_array(strtolower($name), $keys);
  }

  /**
   * Checks if a given value is part of this enum
   * @param mixed $value the value to check for validity
   * @param bool $strict if yes type checks are performed
   * @return bool true if the value is part of the enum and false otherwise
   */
  public static function isValidValue($value, bool $strict = true): bool
  {
    $values = self::getValues();
    return in_array($value, $values, $strict);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">

  /**
   * Gets a case mapping which maps a lower case names to the real enum names
   * @return mixed[]
   */
  private static function getCaseMapping(): array
  {
    if (self::$constCacheArrayCaseMapping == NULL) {
      self::$constCacheArrayCaseMapping = [];
    }
    $called_class = get_called_class();
    if (!array_key_exists($called_class, self::$constCacheArrayCaseMapping)) {
      self::$constCacheArrayCaseMapping[$called_class] = [];
      foreach (self::getNames() as $name) {
        self::$constCacheArrayCaseMapping[$called_class][strtolower($name)] = $name;
      }
    }
    return self::$constCacheArrayCaseMapping[$called_class];
  }

  /**
   * Gets a dictionary of all constants in this enum
   * @return mixed[]
   */
  private static function getConstants(): array
  {
    if (self::$constCacheArray == NULL) {
      self::$constCacheArray = [];
    }
    $called_class = get_called_class();
    if (!array_key_exists($called_class, self::$constCacheArray)) {
      $reflect = new \ReflectionClass($called_class);
      $array = $reflect->getConstants();
      asort($array);
      self::$constCacheArray[$called_class] = $array;
    }
    return self::$constCacheArray[$called_class];
  }
//</editor-fold desc="Private Methods">
}