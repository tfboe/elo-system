<?php
declare(strict_types=1);
/**
 * 2018-01-09: Note first time trying to implement "good" unit tests according to "The Art of Unit Testing"
 */

namespace Tests\Tfboe\FmLib\Service\RankingSystem;

use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyInterface;
use Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier;
use Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService;
use Tfboe\FmLib\Service\RankingSystem\TimeServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\Helpers\UnitTestCase;

/**
 * Class EntityComparerByTimeStartTimeAndLocalIdentifierTest
 * @package Tests\Tfboe\FmLib\Service\RankingSystemListService
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class EntityComparerByTimeStartTimeAndLocalIdentifierTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @return array
   */
  public function localIdentifierProvider()
  {
    return array_merge($this->localIdentifierProviderWithoutZero(), [[8, 8, 0]]);
  }

  /**
   * @return array
   */
  public function localIdentifierProviderWithoutZero()
  {
    return [
      [5, 7, -1],
      ["15", "5", 1]
    ];
  }

  /**
   * @dataProvider timePairProvider
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::compareEntities
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *       ::compareEntityTimes
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *       ::compareLocalIdentifiersWithinTournament
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::getPredecessors
   * @param \DateTime $time1
   * @param \DateTime $time2
   * @param int $expectedResult
   */
  public function testCompareEntitiesDifferentEntityTimesGrandparentLevel(\DateTime $time1, \DateTime $time2,
                                                                          int $expectedResult)
  {
    $service = $this->createComparer();
    $commonEndTime = new \DateTime("2017-01-01");
    $grandParent1 = $this->createTreeStructureEntity('gp1', ['getEndTime' => $time1]);
    $grandParent2 = $this->createTreeStructureEntity('gp2', ['getEndTime' => $time2]);
    $parent1 = $this->createTreeStructureEntity('p1', ['getEndTime' => $commonEndTime, 'getParent' => $grandParent1]);
    $parent2 = $this->createTreeStructureEntity('p2', ['getEndTime' => $commonEndTime, 'getParent' => $grandParent2]);
    $entity1 = $this->createTreeStructureEntity('e1', ['getEndTime' => $commonEndTime, 'getParent' => $parent1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getEndTime' => $commonEndTime, 'getParent' => $parent2]);

    /** @var TournamentHierarchyInterface $entity1 */
    /** @var TournamentHierarchyInterface $entity2 */
    $result = $service->compareEntities($entity1, $entity2);
    self::assertEquals($expectedResult, $result);
  }

  /**
   * @dataProvider timePairProvider
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::compareEntities
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *       ::compareEntityTimes
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *       ::compareLocalIdentifiersWithinTournament
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::getPredecessors
   * @param \DateTime $time1
   * @param \DateTime $time2
   * @param int $expectedResult
   */
  public function testCompareEntitiesDifferentEntityTimesOneLevel(\DateTime $time1, \DateTime $time2,
                                                                  int $expectedResult)
  {
    $service = $this->createComparer();
    $entity1 = $this->createTreeStructureEntity('e1', ['getEndTime' => $time1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getEndTime' => $time2]);

    /** @var TournamentHierarchyInterface $entity1 */
    /** @var TournamentHierarchyInterface $entity2 */
    $result = $service->compareEntities($entity1, $entity2);
    self::assertEquals($expectedResult, $result);
  }

  /**
   * @dataProvider localIdentifierProvider
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::compareEntities
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *        ::compareEntityTimes
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *               ::compareLocalIdentifiersWithinTournament
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::getPredecessors
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @param mixed $localIdentifier1 the local identifier of entity1
   * @param mixed $localIdentifier2 the local identifier of entity2
   * @param int $expectedResult
   */
  public function testCompareEntitiesSameEntityTimesDifferentLocalIdentifiers($localIdentifier1, $localIdentifier2,
                                                                              int $expectedResult)
  {
    $service = $this->createComparer();
    $commonEndTime = new \DateTime("2017-01-01");
    $entity1 = $this->createTreeStructureEntity('e1',
      ['getEndTime' => $commonEndTime, 'getLocalIdentifier' => $localIdentifier1]);
    $entity2 = $this->createTreeStructureEntity('e2',
      ['getEndTime' => $commonEndTime, 'getLocalIdentifier' => $localIdentifier2]);

    /** @var TournamentHierarchyInterface $entity1 */
    /** @var TournamentHierarchyInterface $entity2 */
    $result = $service->compareEntities($entity1, $entity2);
    self::assertEquals($expectedResult, $result);
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //ReflectionException
  /**
   * @dataProvider timePairProvider
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *        ::compareEntityTimes
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @param \DateTime $time1 the start time of the first entity
   * @param \DateTime $time2 the start time of the second entity
   * @param int $expectedResult the expected result of the method
   */
  public function testCompareEntityTimesWithStartTimes(\DateTime $time1, \DateTime $time2, int $expectedResult)
  {
    $service = $this->createComparer();
    $commonEndTime = new \DateTime("2017-12-01");
    $entity1 = $this->createTreeStructureEntity('e1', ['getEndTime' => $commonEndTime, 'getStartTime' => $time1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getEndTime' => $commonEndTime, 'getStartTime' => $time2]);

    /** @noinspection PhpUnhandledExceptionInspection */
    $result = self::getMethod(get_class($service), 'compareEntityTimes')->invokeArgs($service, [$entity1, $entity2]);
    self::assertEquals($expectedResult, $result);
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //ReflectionException
  /**
   * @dataProvider timePairProvider
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *        ::compareEntityTimes
   * @uses         \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   * @uses         \Tfboe\FmLib\Service\RankingSystem\RecursiveEndStartTimeService::getTime
   * @param \DateTime $time1 the end time of the first entity
   * @param \DateTime $time2 the end time of the second entity
   * @param int $expectedResult the expected result of the method
   */
  public function testCompareEntityTimesWithoutStartTimes(\DateTime $time1, \DateTime $time2, int $expectedResult)
  {
    $service = $this->createComparer();
    $entity1 = $this->createTreeStructureEntity('e1', ['getEndTime' => $time1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getEndTime' => $time2]);

    /** @noinspection PhpUnhandledExceptionInspection */
    $result = self::getMethod(get_class($service), 'compareEntityTimes')->invokeArgs($service, [$entity1, $entity2]);
    self::assertEquals($expectedResult, $result);
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //ReflectionException
  /**
   * @dataProvider localIdentifierProviderWithoutZero
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *         ::compareLocalIdentifiersWithinTournament
   * @param mixed $localIdentifier1 the local identifier of grand parent 1
   * @param mixed $localIdentifier2 the local identifier of grand parent 2
   * @param int $expectedResult the expected result of the method
   */
  public function testCompareLocalIdentifiersWithinTournamentGrandParentsLevel($localIdentifier1, $localIdentifier2,
                                                                               int $expectedResult)
  {
    $service = $this->createComparer();
    $grandParent1 = $this->createTreeStructureEntity('gp1', ['getLocalIdentifier' => $localIdentifier1]);
    $grandParent2 = $this->createTreeStructureEntity('gp2', ['getLocalIdentifier' => $localIdentifier2]);
    $parent1 = $this->createTreeStructureEntity('p1', ['getLocalIdentifier' => 1, 'getParent' => $grandParent1]);
    $parent2 = $this->createTreeStructureEntity('p2', ['getLocalIdentifier' => 5, 'getParent' => $grandParent2]);
    $entity1 = $this->createTreeStructureEntity('e1', ['getLocalIdentifier' => "4", 'getParent' => $parent1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getLocalIdentifier' => "2", 'getParent' => $parent2]);

    /** @noinspection PhpUnhandledExceptionInspection */
    $result = self::getMethod(get_class($service), 'compareLocalIdentifiersWithinTournament')
      ->invokeArgs($service, [$entity1, $entity2]);
    self::assertEquals($expectedResult, $result);
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //ReflectionException
  /**
   * @dataProvider localIdentifierProvider
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *         ::compareLocalIdentifiersWithinTournament
   * @param mixed $localIdentifier1 the local identifier of entity1
   * @param mixed $localIdentifier2 the local identifier of entity2
   * @param int $expectedResult the expected result of the method
   */
  public function testCompareLocalIdentifiersWithinTournamentOneLevel($localIdentifier1, $localIdentifier2,
                                                                      int $expectedResult)
  {
    $service = $this->createComparer();
    $entity1 = $this->createTreeStructureEntity('e1', ['getLocalIdentifier' => $localIdentifier1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getLocalIdentifier' => $localIdentifier2]);

    /** @noinspection PhpUnhandledExceptionInspection */
    $result = self::getMethod(get_class($service), 'compareLocalIdentifiersWithinTournament')
      ->invokeArgs($service, [$entity1, $entity2]);
    self::assertEquals($expectedResult, $result);
  }

  /** @noinspection PhpDocMissingThrowsInspection */ //ReflectionException
  /**
   * @dataProvider localIdentifierProviderWithoutZero
   * @covers       \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier
   *         ::compareLocalIdentifiersWithinTournament
   * @param mixed $localIdentifier1 the local identifier of parent 1
   * @param mixed $localIdentifier2 the local identifier of parent 2
   * @param int $expectedResult the expected result of the method
   */
  public function testCompareLocalIdentifiersWithinTournamentParentsLevelWithCommonGrandParent($localIdentifier1,
                                                                                               $localIdentifier2,
                                                                                               int $expectedResult)
  {
    $service = $this->createComparer();
    $grandParent = $this->createTreeStructureEntity('gp1', ['getLocalIdentifier' => 1]);
    $parent1 = $this->createTreeStructureEntity('p1',
      ['getLocalIdentifier' => $localIdentifier1, 'getParent' => $grandParent]);
    $parent2 = $this->createTreeStructureEntity('p2',
      ['getLocalIdentifier' => $localIdentifier2, 'getParent' => $grandParent]);
    $entity1 = $this->createTreeStructureEntity('e1', ['getLocalIdentifier' => "3", 'getParent' => $parent1]);
    $entity2 = $this->createTreeStructureEntity('e2', ['getLocalIdentifier' => "5", 'getParent' => $parent2]);

    /** @noinspection PhpUnhandledExceptionInspection */
    $result = self::getMethod(get_class($service), 'compareLocalIdentifiersWithinTournament')
      ->invokeArgs($service, [$entity1, $entity2]);
    self::assertEquals($expectedResult, $result);
  }

  /**
   * @covers \Tfboe\FmLib\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   */
  public function testConstruct()
  {
    $entity = $this->createComparer();
    self::assertInstanceOf(EntityComparerByTimeStartTimeAndLocalIdentifier::class, $entity);
  }

  /**
   * @return array
   */
  public function timePairProvider()
  {
    return [
      [new \DateTime("2017-05-01"), new \DateTime("2017-06-01"), -1],
      [new \DateTime("2017-06-01"), new \DateTime("2017-06-01"), 0],
      [new \DateTime("2017-06-01"), new \DateTime("2017-05-30"), 1]
    ];
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Creates a default comparer with the given timeService or with the default time service if omitted
   * @param TimeServiceInterface|null $timeService the time service to use
   * @return EntityComparerByTimeStartTimeAndLocalIdentifier the comparer
   */
  private function createComparer(TimeServiceInterface $timeService = null)
  {
    if ($timeService === null) {
      $timeService = new RecursiveEndStartTimeService();
    }
    return new EntityComparerByTimeStartTimeAndLocalIdentifier($timeService);
  }

  /**
   * Creates a tree structure entity with the given id and optionally additional method results
   * @param string $entityId the id of the entity
   * @param array $otherMethods the additional method results
   * @return MockObject the stub
   */
  private function createTreeStructureEntity(string $entityId, array $otherMethods = []): MockObject
  {
    return $this->createStub(TournamentHierarchyInterface::class, array_merge($otherMethods, ['getId' => $entityId]));
  }
//</editor-fold desc="Private Methods">
}