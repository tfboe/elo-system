<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/6/18
 * Time: 7:08 PM
 */

namespace Tests\Helpers;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class UnitTestCase
 * @package Tests\Helpers
 */
abstract class UnitTestCase extends TestCase
{
  use ReflectionMethods;
  use OnlyTestLogging;

//<editor-fold desc="Protected Methods">

  /**
   * Creates a stub with a given set of stubbed methods, which will return the given results
   * @param string $class the class name
   * @param array $methodResults a dictionary mapping method names to results of this methods
   * @return MockObject the configured stub
   */
  protected function createStub(string $class, array $methodResults): MockObject
  {
    $entity = $this->createMock($class);
    foreach ($methodResults as $method => $result) {
      $entity->method($method)->willReturn($result);
    }
    return $entity;
  }

  /**
   * Creates an empty mock with a getId method
   * @param string $class the class to mock
   * @param string $entityId the id to assign
   * @param string $getterMethod the name of the getter method
   * @return \PHPUnit\Framework\MockObject\MockObject the mocked instance
   */
  protected function createStubWithId(string $class, $entityId = "entity-id", $getterMethod = 'getId')
  {
    return $this->createStub($class, [$getterMethod => $entityId]);
  }
//</editor-fold desc="Protected Methods">
}