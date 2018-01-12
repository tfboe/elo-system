<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:32 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Entity\RankingSystemList;
use App\Entity\Tournament;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;


/**
 * Class RankingSystem
 * @package App\Service\RankingSystem
 */
abstract class RankingSystem implements RankingSystemInterface
{
//<editor-fold desc="Fields">
  /** @var EntityManagerInterface */
  protected $entityManager;

  /** @var TimeServiceInterface */
  protected $timeService;

  /** @var EntityComparerInterface */
  protected $entityComparer;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * RankingSystem constructor.
   * @param EntityManagerInterface $entityManager
   * @param TimeServiceInterface $timeService
   * @param EntityComparerInterface $entityComparer
   */
  public function __construct(EntityManagerInterface $entityManager, TimeServiceInterface $timeService,
                              EntityComparerInterface $entityComparer)
  {
    $this->entityManager = $entityManager;
    $this->timeService = $timeService;
    $this->entityComparer = $entityComparer;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getEarliestInfluence(\App\Entity\RankingSystem $ranking, Tournament $tournament): ?\DateTime
  {
    return $this->getEarliestEntityInfluence($ranking, $tournament, false);
  }

  /**
   * @inheritdoc
   */
  public function updateRankingForTournament(\App\Entity\RankingSystem $ranking, Tournament $tournament,
                                             ?\DateTime $oldInfluence)
  {
    $earliestInfluence = $this->getEarliestInfluence($ranking, $tournament);
    if ($oldInfluence !== null &&
      ($earliestInfluence === null || $oldInfluence < $earliestInfluence)) {
      $earliestInfluence = $oldInfluence;
    }
    if ($earliestInfluence !== null) {
      $this->updateRankingFrom($ranking, $earliestInfluence);
    }
  }

  /**
   * @inheritDoc
   */
  public function updateRankingFrom(\App\Entity\RankingSystem $ranking, \DateTime $from)
  {
    //find first reusable
    /** @var RankingSystemList[] $lists */
    $lists = array_values($ranking->getLists()->toArray());

    $current = null;
    /** @var RankingSystemList $lastReusable */
    $lastReusable = null;
    $toUpdate = [];

    foreach ($lists as $list) {
      if ($list->isCurrent()) {
        $current = $list;
      } else if ($list->getLastEntryTime() >= $from) {
        $toUpdate[] = $list;
      } else if ($lastReusable === null || $list->getLastEntryTime() > $lastReusable->getLastEntryTime()) {
        $lastReusable = $list;
      }
    }

    if ($lastReusable === null) {
      $lastReusable = new RankingSystemList();
    }

    usort($toUpdate, function (RankingSystemList $list1, RankingSystemList $list2) {
      return $list1->getLastEntryTime() <=> $list2->getLastEntryTime();
    });

    $entities = $this->getEntities($ranking, $lastReusable->getLastEntryTime());
    //sort entities
    $this->timeService->clearTimes();
    usort($entities, function ($entity1, $entity2) {
      return $this->entityComparer->compareEntities($entity1, $entity2);
    });

    $nextEntityIndex = 0;
    foreach ($toUpdate as $list) {
      $this->recomputeBasedOn($list, $lastReusable, $entities, $nextEntityIndex);
      $lastReusable = $list;
    }

    if ($current === null) {
      $current = new RankingSystemList();
      $current->setCurrent(true);
      $this->entityManager->persist($current);
      $current->setRankingSystem($ranking);
    }
    $this->recomputeBasedOn($current, $lastReusable, $entities, $nextEntityIndex);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets the relevant entities for updating
   * @param \App\Entity\RankingSystem $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return TreeStructureEntityInterface[]
   */
  protected function getEntities(\App\Entity\RankingSystem $ranking, \DateTime $from): array
  {
    $query = $this->getEntitiesQueryBuilder($ranking, $from);
    return $query->getQuery()->getResult();
  }

  /**
   * Gets a query for getting the relevant entities for updating
   * @param \App\Entity\RankingSystem $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return QueryBuilder
   */
  protected abstract function getEntitiesQueryBuilder(\App\Entity\RankingSystem $ranking,
                                                      \DateTime $from): QueryBuilder;

  /**
   * Gets the level of the ranking system service (see Level Enum)
   * @return int
   */
  protected abstract function getLevel(): int;
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Clones all ranking values from base and inserts them into list, furthermore removes all remaining ranking values of
   * list. After this method was called list and base contain exactly the same rankings.
   * @param RankingSystemList $list the ranking list to change
   * @param RankingSystemList $base the ranking list to use as base list, this doesn't get changed
   */
  private function cloneInto(RankingSystemList $list, RankingSystemList $base)
  {
    //TODO
  }

  /**
   * Gets the earliest influence for the given entity
   * @param \App\Entity\RankingSystem $ranking the ranking system for which to get the influence
   * @param TreeStructureEntityInterface $entity the entity to analyze
   * @param bool $parentIsRanked true iff a predecessor contained the given ranking in its ranking systems
   * @return \DateTime|null the earliest influence or null if $parentIsRanked is false and the entity and all its
   *                        successors do not have the ranking in its ranking systems
   * @throws \App\Exceptions\ValueNotSet the ranking has no id
   */
  private function getEarliestEntityInfluence(\App\Entity\RankingSystem $ranking, TreeStructureEntityInterface $entity,
                                              bool $parentIsRanked): ?\DateTime
  {
    $this->timeService->clearTimes();
    $entityIsRanked = $parentIsRanked || $entity->getRankingSystems()->containsKey($ranking->getId());
    if ($entity->getLevel() === $this->getLevel()) {
      if ($entityIsRanked) {
        return $this->timeService->getTime($entity);
      } else {
        return null;
      }
    }
    $result = null;

    foreach ($entity->getChildren() as $child) {
      $earliest = $this->getEarliestEntityInfluence($ranking, $child, $entityIsRanked);
      if ($result === null || ($earliest !== null && $earliest < $result)) {
        $result = $earliest;
      }
    }
    return $result;
  }

  /**
   * Recomputes the given ranking list by using base as base list and applying the changes for the given entities
   * starting from the given index. If list is not the current list only the entities up to $list->getLastEntryTime()
   * are applied and the index gets changed accordingly.
   * @param RankingSystemList $list the list to recompute
   * @param RankingSystemList $base the list to use as base
   * @param TreeStructureEntityInterface[] $entities the list of entities to use for the computation
   * @param int $nextEntityIndex the first index in the entities list to consider
   */
  private function recomputeBasedOn(RankingSystemList $list, RankingSystemList $base, array $entities,
                                    int &$nextEntityIndex)
  {
    $this->cloneInto($list, $base);
    for ($i = $nextEntityIndex; $i < count($entities); $i++) {
      $time = $this->timeService->getTime($entities[$i]);
      if (!$list->isCurrent() && $time > $list->getLastEntryTime()) {
        $nextEntityIndex = $i;
        return;
      }
      //TODO compute ranking changes for this entity and apply it to $list

    }
    $nextEntityIndex = count($entities);
  }
//</editor-fold desc="Private Methods">
}