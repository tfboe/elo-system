<?php
declare(strict_types=1);

/**
 * Class TestCase
 */

namespace Tests\Helpers;


use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase;

/**
 * Class TestCase
 * @package Tests\Helpers
 */
abstract class LumenTestCase extends TestCase
{
  use ReflectionMethods;
  use OnlyTestLogging;

//<editor-fold desc="Public Methods">

  /**
   * Creates the application.
   *
   * @return \Laravel\Lumen\Application
   */
  public function createApplication(): Application
  {
    return require __DIR__ . '/../../bootstrap/app.php';
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Checks for a given object if the given properties got set correctly by the query data.
   * @param mixed[] $data the request data
   * @param mixed $object the object whose properties to check
   * @param mixed[] $properties the properties to check, property name maps to the default value (if not set in request)
   * @param mixed[] $enumProperties the enum properties to check, property name maps to an info array, which contains
   *                                the enum name and the default value
   */
  protected function checkProperties(array $data, $object, array $properties, array $enumProperties = [])
  {
    foreach ($properties as $property => $default) {
      $getter = 'get' . ucfirst($property);
      if (!method_exists($object, $getter)) {
        $getter = 'is' . ucfirst($property);
      }
      $transformer = null;
      if (is_array($default) && array_key_exists('transformer', $default)) {
        $transformer = $default['transformer'];
        $default = $default['default'];
      }
      if (array_key_exists($property, $data)) {
        $value = $data[$property];
        if ($transformer != null) {
          $value = $transformer($value);
        }
        self::assertEquals($value, $object->$getter());
      } else {
        self::assertEquals($default, $object->$getter());
      }
    }

    foreach ($enumProperties as $property => $info) {
      $enumClass = $info['enum'];
      $default = $info['default'];
      $getter = 'get' . ucfirst($property);
      if (array_key_exists($property, $data)) {
        $name = $data[$property];
        /** @noinspection PhpUndefinedMethodInspection */
        self::assertEquals($enumClass::getValue($name), $object->$getter());
      } else {
        self::assertEquals($default, $object->$getter());
      }
    }
  }
//</editor-fold desc="Protected Methods">
}
