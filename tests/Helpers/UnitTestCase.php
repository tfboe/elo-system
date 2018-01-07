<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/6/18
 * Time: 7:08 PM
 */

namespace Tests\Helpers;

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
   * Creates an empty mock with a getId method
   * @param string $class the class to mock
   * @param string $id
   * @return \PHPUnit\Framework\MockObject\MockObject the mocked instance
   */
  protected function createMockWithId(string $class, $id = "entity-id")
  {
    $entity = $this->createMock($class);
    $entity->method('getId')->willReturn($id);
    return $entity;
  }
//</editor-fold desc="Protected Methods">
}