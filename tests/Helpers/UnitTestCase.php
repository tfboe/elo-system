<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/6/18
 * Time: 7:08 PM
 */

namespace Tests\Helpers;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
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

  /**
   * Gets a mock for an entity manager which creates a query builder which will return a query which will return the
   * given result.
   * @param array $result the result array the query should return
   * @param string|null $expectedQuery the expected query if set
   * @param string[] $otherMockedMethods list of other methods to mock
   * @return MockObject the mocked entity manager
   */
  protected function getEntityManagerMockForQuery(array $result, ?string $expectedQuery = null,
                                                  array $otherMockedMethods = [])
  {
    $entityManager = $this->getMockForAbstractClass(EntityManager::class, [], '',
      false, true, true, array_merge($otherMockedMethods, ['createQueryBuilder']));
    $queryBuilder = $this->getMockForAbstractClass(QueryBuilder::class, [$entityManager],
      '', true, true, true, ['getQuery']);
    $query = $this->createMock(AbstractQuery::class);
    $query->expects(static::once())->method('getResult')->willReturn($result);
    if ($expectedQuery !== null) {
      $queryBuilder->expects(static::once())->method('getQuery')->willReturnCallback(
        function () use ($queryBuilder, $query, $expectedQuery) {
          /** @var QueryBuilder $queryBuilder */
          self::assertEquals($expectedQuery, $queryBuilder->getDQL());
          return $query;
        });
    } else {
      $queryBuilder->expects(static::once())->method('getQuery')->willReturn($query);
    }
    $entityManager->expects(static::once())->method('createQueryBuilder')->willReturn($queryBuilder);
    return $entityManager;
  }
//</editor-fold desc="Protected Methods">
}