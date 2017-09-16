<?php

abstract class TestCase extends Laravel\Lumen\Testing\TestCase
{
//<editor-fold desc="Public Methods">
  /**
   * Creates the application.
   *
   * @return \Laravel\Lumen\Application
   */
  public function createApplication()
  {
    return require __DIR__ . '/../bootstrap/app.php';
  }

  protected static function getMethod(string $class, string $name)
  {
    $class = new ReflectionClass($class);
    $method = $class->getMethod($name);
    $method->setAccessible(true);
    return $method;
  }

  protected static function getProperty(string $class, string $name)
  {
    $class = new ReflectionClass($class);
    $property = $class->getProperty($name);
    $property->setAccessible(true);
    return $property;
  }
//</editor-fold desc="Public Methods">
}
