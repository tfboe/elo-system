<?php
declare(strict_types=1);

/**
 * Class TestCase
 */

namespace Tests\Helpers;

use Laravel\Lumen\Application;

/**
 * Class TestCase
 * @package Tests\Helpers
 */
abstract class TestCase extends \Laravel\Lumen\Testing\TestCase
{
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
      $enum_class = $info['enum'];
      $default = $info['default'];
      $getter = 'get' . ucfirst($property);
      if (array_key_exists($property, $data)) {
        $name = $data[$property];
        /** @noinspection PhpUndefinedMethodInspection */
        self::assertEquals($enum_class::getValue($name), $object->$getter());
      } else {
        self::assertEquals($default, $object->$getter());
      }
    }
  }

  /**
   * Gets a protected or private method and makes it accessible
   * @param string $class the class name
   * @param string $name the method name
   * @return \ReflectionMethod the accessible method object
   */
  protected static function getMethod(string $class, string $name): \ReflectionMethod
  {
    $class = new \ReflectionClass($class);
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  /**
   * Calls the given protected method on the given object with the given arguments
   * @param mixed $object the object to call on
   * @param string $method the method name
   * @param mixed[] $args the arguments for the method
   * @return mixed the return value of the method
   */
  protected static function callProtectedMethod($object, string $method, array $args = [])
  {
    return self::getMethod(get_class($object), $method)->invokeArgs($object, $args);
  }

  /**
   * Gets a protected or private property and makes it accessible
   * @param string $class the class name
   * @param string $name the method name
   * @return \ReflectionProperty the accessible property object
   */
  protected static function getProperty(string $class, string $name): \ReflectionProperty
  {
    $class = new \ReflectionClass($class);
    $property = $class->getProperty($name);
    $property->setAccessible(true);
    return $property;
  }
//</editor-fold desc="Protected Methods">
}
