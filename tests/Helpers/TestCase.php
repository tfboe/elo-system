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
//</editor-fold desc="Public Methods">
}
