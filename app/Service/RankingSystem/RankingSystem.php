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
  protected $em;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * GameRankingSystem constructor.
   * @param EntityManagerInterface $entityManager
   */
  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->em = $entityManager;
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
                                             ?\DateTime $earliest_old_influence): void
  {
    $earliest_influence = $this->getEarliestInfluence($ranking, $tournament);
    if ($earliest_old_influence !== null &&
      ($earliest_influence === null || $earliest_old_influence < $earliest_influence)) {
      $earliest_influence = $earliest_old_influence;
    }
    if ($earliest_influence !== null) {
      $this->updateRankingFrom($ranking, $earliest_influence);
    }
  }

  /**
   * @inheritDoc
   */
  public function updateRankingFrom(\App\Entity\RankingSystem $ranking, \DateTime $from): void
  {
    //find first reusable
    /** @var RankingSystemList[] $lists */
    $lists = array_values($ranking->getLists()->toArray());

    $current = null;
    /** @var RankingSystemList $last_reusable */
    $last_reusable = null;
    $to_update = [];

    foreach ($lists as $list) {
      if ($list->isCurrent()) {
        $current = $list;
      } else if ($list->getLastEntryTime() >= $from) {
        $to_update[] = $list;
      } else if ($last_reusable === null || $list->getLastEntryTime() > $last_reusable->getLastEntryTime()) {
        $last_reusable = $list;
      }
    }

    if ($last_reusable === null) {
      $last_reusable = new RankingSystemList();
    }

    usort($to_update, function (RankingSystemList $l1, RankingSystemList $l2) {
      return $l1->getLastEntryTime() <=> $l2->getLastEntryTime();
    });

    $entities = $this->getEntities($ranking, $last_reusable->getLastEntryTime());
    $this->sortEntities($entities);

    $next_entity_index = 0;
    foreach ($to_update as $list) {
      $this->recomputeBasedOn($list, $last_reusable, $entities, $next_entity_index);
      $last_reusable = $list;
    }

    if ($current === null) {
      $current = new RankingSystemList();
      $current->setCurrent(true);
      $this->em->persist($current);
      $current->setRankingSystem($ranking);
    }
    $this->recomputeBasedOn($current, $last_reusable, $entities, $next_entity_index);
  }

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
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Compare two entities.
   * Precondition: $e1->getLevel() === $e2->getLevel()
   * @param TreeStructureEntityInterface $e1 First entity to compare
   * @param TreeStructureEntityInterface $e2 Second entity to compare
   * @param \DateTime[] $times a dictionary containing the times of all relevant entities (the entities from getEntities
   *                           + all Predecessors)
   * @return int -1 if $e1 < $e2, 1 if $e1 > $e2 and 0 if $e1 == $e2
   */
  protected function compareEntities(TreeStructureEntityInterface $e1, TreeStructureEntityInterface $e2, array $times): int
  {
    $tmp_e1 = $e1;
    $tmp_e2 = $e2;
    while ($tmp_e1 !== null && $tmp_e2 !== null && $tmp_e1->getId() !== $tmp_e2->getId()) {
      if ($times[$tmp_e1->getId()] < $times[$tmp_e2->getId()]) {
        return -1;
      } else if ($times[$tmp_e1->getId()] > $times[$tmp_e2->getId()]) {
        return 1;
      }
      if ($tmp_e1->getStartTime() !== null && $tmp_e2->getStartTime() !== null) {
        if ($tmp_e1->getStartTime() < $tmp_e2->getStartTime()) {
          return -1;
        } else if ($tmp_e1->getStartTime() > $tmp_e2->getStartTime()) {
          return 1;
        }
      }
      $tmp_e1 = $tmp_e1->getParent();
      $tmp_e2 = $tmp_e2->getParent();
    }

    //compare unique identifiers within tournament
    $predecessors1 = $this->getPredecessors($e1);
    $predecessors2 = $this->getPredecessors($e2);

    for ($i = count($predecessors1) - 1; $i >= 0; $i--) {
      if ($predecessors1[$i]->getLocalIdentifier() !== $predecessors2[$i]->getLocalIdentifier()) {
        return $predecessors1[$i]->getLocalIdentifier() <=> $predecessors2[$i]->getLocalIdentifier();
      }
    }
    // the two entities are equal
    return 0;
  }


  /**
   * Gets a query for getting the relevant entities for updating
   * @param \App\Entity\RankingSystem $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return QueryBuilder
   */
  protected abstract function getEntitiesQueryBuilder(\App\Entity\RankingSystem $ranking, \DateTime $from): QueryBuilder;

  /**
   * Gets the level of the ranking system service (see Level Enum)
   * @return int
   */
  protected abstract function getLevel(): int;

  /**
   * Gets the relevant time for the given entity for ordering purposes
   * @param TreeStructureEntityInterface $entity
   * @return \DateTime
   */
  protected function getTime(TreeStructureEntityInterface $entity): \DateTime
  {
    $result = $entity->getEndTime();
    if ($result != null) {
      return $result;
    }
    $result = $entity->getStartTime();
    if ($result != null) {
      return $result;
    }
    if ($entity->getParent() !== null) {
      return $this->getTime($entity->getParent());
    }
    //entity must be a tournament
    /** @var Tournament $entity */
    return $entity->getUpdatedAt();
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Clones all ranking values from base and inserts them into list, furthermore removes all remaining ranking values of
   * list. After this method was called list and base contain exactly the same rankings.
   * @param RankingSystemList $list the ranking list to change
   * @param RankingSystemList $base the ranking list to use as base list, this doesn't get changed
   */
  private function cloneInto(RankingSystemList $list, RankingSystemList $base): void
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
    $entity_is_ranked = $parentIsRanked || $entity->getRankingSystems()->containsKey($ranking->getId());
    if ($entity->getLevel() === $this->getLevel()) {
      if ($entity_is_ranked) {
        return $this->getTime($entity);
      } else {
        return null;
      }
    }
    $result = null;

    foreach ($entity->getChildren() as $child) {
      $earliest = $this->getEarliestEntityInfluence($ranking, $child, $entity_is_ranked);
      if ($result === null || ($earliest !== null && $earliest < $result)) {
        $result = $earliest;
      }
    }
    return $result;
  }

  /**
   * Gets a list of all predecessors of the given entity $e (inclusive $e itself).
   * @param TreeStructureEntityInterface $e the entity for which to get the predecessors
   * @return TreeStructureEntityInterface[] the predecessors of $e inclusive $e
   */
  private function getPredecessors(TreeStructureEntityInterface $e): array
  {
    $res = [];
    while ($e !== null) {
      $res[] = $e;
      $e = $e->getParent();
    }
    return $res;
  }

  /**
   * Recomputes the given ranking list by using base as base list and applying the changes for the given entities
   * starting from the given index. If list is not the current list only the entities up to $list->getLastEntryTime()
   * are applied and the index gets changed accordingly.
   * @param RankingSystemList $list the list to recompute
   * @param RankingSystemList $base the list to use as base
   * @param TreeStructureEntityInterface[] $entities the list of entities to use for the computation
   * @param int $next_entity_index the first index in the entities list to consider
   */
  private function recomputeBasedOn(RankingSystemList $list, RankingSystemList $base, array $entities,
                                    int &$next_entity_index): void
  {
    $this->cloneInto($list, $base);
    for ($i = $next_entity_index; $i < count($entities); $i++) {
      $time = $this->getTime($entities[$i]);
      if (!$list->isCurrent() && $time > $list->getLastEntryTime()) {
        $next_entity_index = $i;
        return;
      }
      //TODO compute ranking changes for this entity and apply it to $list
    }
    $next_entity_index = count($entities);
  }

  /**
   *
   * @param TreeStructureEntityInterface[] $entities
   */
  private function sortEntities(array $entities): void
  {
    //compute times
    $times = [];
    foreach ($entities as $e) {
      $entity = $e;
      while ($entity !== null && !array_key_exists($entity->getId(), $times)) {
        $times[$entity->getId()] = $this->getTime($entity);
        $entity = $entity->getParent();
      }
    }

    usort($entities, function ($e1, $e2) use ($times) {
      return $this->compareEntities($e1, $e2, $times);
    });
  }
//</editor-fold desc="Private Methods">
}