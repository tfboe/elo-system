<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/7/18
 * Time: 9:22 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Helpers\TournamentHierarchyInterface;


/**
 * Class EntityComparerByTimeStartTimeAndLocalIdentifier
 * @package App\Service\RankingSystemListService
 */
class EntityComparerByTimeStartTimeAndLocalIdentifier implements EntityComparerInterface
{
//<editor-fold desc="Fields">
  /** @var TimeServiceInterface */
  protected $timeService;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * EntityComparerByTimeStartTimeAndLocalIdentifier constructor.
   * @param TimeServiceInterface $timeService
   */
  public function __construct(TimeServiceInterface $timeService)
  {
    $this->timeService = $timeService;
  }
//</editor-fold desc="Constructor">


//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function compareEntities(TournamentHierarchyInterface $entity1, TournamentHierarchyInterface $entity2): int
  {
    $tmpE1 = $entity1;
    $tmpE2 = $entity2;
    while ($tmpE1 !== null && $tmpE2 !== null && $tmpE1->getId() !== $tmpE2->getId()) {
      $comparison = $this->compareEntityTimes($tmpE1, $tmpE2);
      if ($comparison !== 0) {
        return $comparison;
      }
      $tmpE1 = $tmpE1->getParent();
      $tmpE2 = $tmpE2->getParent();
    }

    return $this->compareLocalIdentifiersWithinTournament($entity1, $entity2);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Compares the times of the two entities using the time array and using their start times
   * @param TournamentHierarchyInterface $entity1 the first entity to compare
   * @param TournamentHierarchyInterface $entity2 the second entity to compare
   * @return int returns -1 if the relevant times of entity1 are before the relevant times of entity2, 1 if the times of
   *             entity2 are before the times of entity1 and 0 if they have the same times
   */
  protected function compareEntityTimes(TournamentHierarchyInterface $entity1,
                                        TournamentHierarchyInterface $entity2)
  {
    $time1 = $this->timeService->getTime($entity1);
    $time2 = $this->timeService->getTime($entity2);
    if ($time1 < $time2) {
      return -1;
    } else if ($time1 > $time2) {
      return 1;
    }
    if ($entity1->getStartTime() !== null && $entity2->getStartTime() !== null) {
      if ($entity1->getStartTime() < $entity2->getStartTime()) {
        return -1;
      } else if ($entity1->getStartTime() > $entity2->getStartTime()) {
        return 1;
      }
    }
    return 0;
  }

  /**
   * Compares the local identifiers of the predecessors of the given entities, beginning from the tournament level
   * @param TournamentHierarchyInterface $entity1 the first entity to compare
   * @param TournamentHierarchyInterface $entity2 the second entity to compare
   * @return int returns -1 if the first predecessor with a lower local identifier is of entity1, 1 if it is of entity2
   *             and 0 if the two entities are equal (<=> all predecessors have same local identifiers)
   */
  protected function compareLocalIdentifiersWithinTournament(TournamentHierarchyInterface $entity1,
                                                             TournamentHierarchyInterface $entity2)
  {
    //compare unique identifiers within tournament
    $predecessors1 = $this->getPredecessors($entity1);
    $predecessors2 = $this->getPredecessors($entity2);

    for ($i = count($predecessors1) - 1; $i >= 0; $i--) {
      if ($predecessors1[$i]->getLocalIdentifier() !== $predecessors2[$i]->getLocalIdentifier()) {
        return $predecessors1[$i]->getLocalIdentifier() <=> $predecessors2[$i]->getLocalIdentifier();
      }
    }

    //the two entities are equal
    return 0;
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Gets a list of all predecessors of the given entity $entity (inclusive $entity itself).
   * @param TournamentHierarchyInterface $entity the entity for which to get the predecessors
   * @return TournamentHierarchyInterface[] the predecessors of $entity inclusive $entity
   */
  private function getPredecessors(TournamentHierarchyInterface $entity): array
  {
    $res = [];
    while ($entity !== null) {
      $res[] = $entity;
      $entity = $entity->getParent();
    }
    return $res;
  }
//</editor-fold desc="Private Methods">
}