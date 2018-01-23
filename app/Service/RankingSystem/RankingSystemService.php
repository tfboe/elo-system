<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:32 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Helpers\TournamentHierarchyInterface;
use App\Entity\Player;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemChange;
use App\Entity\RankingSystemList;
use App\Entity\RankingSystemListEntry;
use App\Entity\Tournament;
use App\Exceptions\PreconditionFailedException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;


/**
 * Class RankingSystemService
 * @package App\Service\RankingSystemService
 * @SuppressWarnings(PHPMD) TODO: refactor this class and remove suppress warnings
 */
abstract class RankingSystemService implements RankingSystemInterface
{
//<editor-fold desc="Fields">
  /** @var EntityManagerInterface */
  protected $entityManager;

  /** @var TimeServiceInterface */
  protected $timeService;

  /** @var EntityComparerInterface */
  protected $entityComparer;

  /**
   * @var RankingSystemChange[][][]
   * first key: tournament hierarchy entity id
   * second key: ranking system id
   * third key: player id
   */
  private $changes;

  /**
   * @var RankingSystemChange[][][]
   * first key: tournament hierarchy entity id
   * second key: ranking system id
   * third key: player id
   */
  private $deletedChanges;

  /**
   * List of ranking systems for which update ranking got already called, indexed by id
   * @var RankingSystemService[]
   */
  private $updateRankingCalls;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * RankingSystemService constructor.
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
    $this->changes = [];
    $this->deletedChanges = [];
    $this->updateRankingCalls = [];
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getEarliestInfluence(RankingSystem $ranking, Tournament $tournament): ?\DateTime
  {
    return $this->getEarliestEntityInfluence($ranking, $tournament, false);
  }

  /**
   * @inheritdoc
   */
  public function updateRankingForTournament(RankingSystem $ranking, Tournament $tournament,
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
  public function updateRankingFrom(RankingSystem $ranking, \DateTime $from)
  {
    // can only be called once per ranking system!!!
    if (array_key_exists($ranking->getId(), $this->updateRankingCalls)) {
      throw new PreconditionFailedException();
    }
    $this->updateRankingCalls[$ranking->getId()] = $ranking;
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

    $this->deleteOldChanges($ranking, $entities);

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

//<editor-fold desc="Protected Final Methods">
  /**
   * Computes the average rating of the given entries
   * @param RankingSystemListEntry[] $entries
   * @return float
   * @throws \App\Exceptions\ValueNotSet at least one entry has no points
   */
  protected final function getAverage(array $entries): float
  {
    $sum = 0.0;
    foreach ($entries as $entry) {
      $sum += $entry->getPoints();
    }
    if (count($entries) === 0) {
      return 0.0;
    } else {
      return $sum / count($entries);
    }
  }

  /**
   * Gets the relevant entities for updating
   * @param RankingSystem $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return TournamentHierarchyEntity[]
   */
  protected final function getEntities(RankingSystem $ranking, \DateTime $from): array
  {
    $query = $this->getEntitiesQueryBuilder($ranking, $from);
    return $query->getQuery()->getResult();
  }

  /**
   * @param Collection|Player[] $players
   * @param RankingSystemList $list
   * @return RankingSystemListEntry[] $entries
   * @throws \App\Exceptions\ValueNotSet at least one player has no id
   */
  protected final function getEntriesOfPlayers(Collection $players, RankingSystemList $list): array
  {
    $result = [];
    foreach ($players as $player) {
      $result[] = $this->getOrCreateRankingSystemListEntry($list, $player);
    }
    return $result;
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * Gets or creates a tournament system change entry for the given entity, ranking and player.
   * @param TournamentHierarchyEntity $entity the tournament hierarchy entity to search for
   * @param RankingSystem $ranking the ranking system to search for
   * @param Player $player the player to search for
   * @return RankingSystemChange the found or newly created ranking system change
   * @throws \App\Exceptions\ValueNotSet either entity, ranking or player has no id
   */
  protected final function getOrCreateChange(TournamentHierarchyEntity $entity, RankingSystem $ranking,
                                             Player $player)
  {
    $key1 = $entity->getId();
    $key2 = $ranking->getId();
    $key3 = $player->getPlayerId();
    if (!array_key_exists($key1, $this->changes) || !array_key_exists($key2, $this->changes[$key1]) ||
      !array_key_exists($key3, $this->changes[$key1][$key2])) {
      /** @var RankingSystemChange[] $changes */
      $changes = $this->entityManager->getRepository(RankingSystemChange::class)->findBy(
        ['hierarchyEntity' => $entity]);
      $this->changes[$key1] = [];
      foreach ($changes as $change) {

        /** @noinspection PhpUnhandledExceptionInspection change comes from the database and therefore has a ranking
         * system with id*/
        $newKey2 = $change->getRankingSystem()->getId();
        /** @noinspection PhpUnhandledExceptionInspection change comes from the database and therefore has a player with
         * id*/
        $newKey3 = $change->getPlayer()->getPlayerId();
        if (!array_key_exists($key1, $this->deletedChanges) || !array_key_exists($key2, $this->deletedChanges[$key1]) ||
          !array_key_exists($key3, $this->deletedChanges[$key1][$key2])) {
          if (!array_key_exists($newKey2, $this->changes)) {
            $this->changes[$key1][$newKey2] = [];
          }
          $this->changes[$key1][$newKey2][$newKey3] = $change;
        }
      }
    }
    if (!array_key_exists($key2, $this->changes[$key1]) || !array_key_exists($key3, $this->changes[$key1][$key2])) {
      //create new change
      $change = new RankingSystemChange(array_keys($this->getAdditionalFields()));
      foreach ($this->getAdditionalFields() as $field => $value) {
        /** @noinspection PhpUnhandledExceptionInspection */ // we know for sure that the property exists
        //(see 2 lines above)
        $change->setProperty($field, 0);
      }
      $change->setHierarchyEntity($entity);
      $change->setRankingSystem($ranking);
      $change->setPlayer($player);
      $this->entityManager->persist($change);
      $this->changes[$key1][$key2][$key3] = $change;
      if (array_key_exists($key1, $this->deletedChanges) && array_key_exists($key2, $this->deletedChanges[$key1]) &&
        array_key_exists($key3, $this->deletedChanges[$key1][$key2])) {
        unset($this->deletedChanges[$key1][$key2][$key3]);
      }
    }
    return $this->changes[$key1][$key2][$key3];
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @param RankingSystemList $list the list in which to search for the entry or in which to add it
   * @param Player $player the player to search for
   * @return RankingSystemListEntry the found or the new entry
   * @throws \App\Exceptions\ValueNotSet player has no id
   */
  protected final function getOrCreateRankingSystemListEntry(RankingSystemList $list,
                                                             Player $player): RankingSystemListEntry
  {
    $playerId = $player->getPlayerId();
    if (!$list->getEntries()->containsKey($playerId)) {
      $entry = new RankingSystemListEntry(array_keys($this->getAdditionalFields()));
      $entry->setPoints($this->startPoints());
      $entry->setPlayer($player);
      $entry->setRankingSystemList($list);
      foreach ($this->getAdditionalFields() as $field => $value) {
        /** @noinspection PhpUnhandledExceptionInspection */ // we know for sure that the property exists
        //(see 4 lines above)
        $entry->setProperty($field, $value);
      }
      $this->entityManager->persist($entry);
    }
    return $list->getEntries()->get($playerId);
  }
//</editor-fold desc="Protected Final Methods">

//<editor-fold desc="Protected Methods">
  /**
   * Gets additional fields for this ranking type mapped to its start value
   * @return string[] list of additional fields
   */
  protected abstract function getAdditionalFields(): array;

  /**
   * Gets all ranking changes for the given entity for the given list. Must return a change for each involved player.
   * The field pointsAfterwards get calculated afterwards and can be left empty.
   * @param TournamentHierarchyEntity $entity the entity for which to compute the ranking changes
   * @param RankingSystemList $list the list for which to compute the ranking changes
   * @return RankingSystemChange[] the changes
   */
  protected abstract function getChanges(TournamentHierarchyEntity $entity, RankingSystemList $list): array;

  /**
   * Gets a query for getting the relevant entities for updating
   * @param RankingSystem $ranking the ranking for which to get the entities
   * @param \DateTime $from search for entities with a time value LARGER than $from, i.e. don't search for entities with
   *                        time value exactly $from
   * @return QueryBuilder
   */
  protected abstract function getEntitiesQueryBuilder(RankingSystem $ranking,
                                                      \DateTime $from): QueryBuilder;

  /**
   * Gets the level of the ranking system service (see Level Enum)
   * @return int
   */
  protected abstract function getLevel(): int;

  /**
   * Gets the start points for a new player in the ranking
   * @return float
   */
  protected function startPoints(): float
  {
    return 0.0;
  }
//</editor-fold desc="Protected Methods">

//<editor-fold desc="Private Methods">
  /**
   * Clones all ranking values from base and inserts them into list, furthermore removes all remaining ranking values of
   * list. After this method was called list and base contain exactly the same rankings.
   * @param RankingSystemList $list the ranking list to change
   * @param RankingSystemList $base the ranking list to use as base list, this doesn't get changed
   * @throws \App\Exceptions\ValueNotSet at least one entry of base has no points or no player or a player with no id
   */
  private function cloneInto(RankingSystemList $list, RankingSystemList $base)
  {
    /*//first remove all entries from list
    foreach($list->getEntries()->toArray() as $entry)
    {
      $list->getEntries()->removeElement($entry);
      $this->entityManager->remove($entry);
    }*/

    $clonedPlayers = [];

    foreach ($base->getEntries() as $entry) {
      $playerId = $entry->getPlayer()->getPlayerId();
      $clonedPlayers[$playerId] = true;
      if (!$list->getEntries()->containsKey($playerId)) {
        //create new entry
        $clone = new RankingSystemListEntry([]);
        $clone->cloneSubClassDataFrom($entry);
        $this->entityManager->persist($clone);
        $clone->setPlayer($entry->getPlayer());
        $clone->setRankingSystemList($list);
      }
      $foundEntry = $list->getEntries()[$playerId];
      $foundEntry->setNumberRankedEntities($entry->getNumberRankedEntities());
      $foundEntry->setPoints($entry->getPoints());
    }

    //remove all unused entries from list
    foreach ($list->getEntries()->toArray() as $playerId => $entry) {
      if (!array_key_exists($playerId, $clonedPlayers)) {
        $list->getEntries()->removeElement($entry);
        $this->entityManager->remove($entry);
      }
    }
  }


  /**
   * @param RankingSystem $ranking
   * @param TournamentHierarchyEntity[] $entities
   * @throws \App\Exceptions\ValueNotSet ranking has no id
   */
  private function deleteOldChanges(RankingSystem $ranking, array $entities)
  {
    //delete old changes
    $queryBuilder = $this->entityManager->createQueryBuilder();
    /** @var RankingSystemChange[] $changes */
    $changes = $queryBuilder
      ->from(RankingSystemChange::class, 'c')
      ->select('c')
      ->where($queryBuilder->expr()->eq('c.rankingSystem', $ranking))
      ->where($queryBuilder->expr()->in('c.hierarchyEntity', ':entities'))
      ->setParameter('entities', $entities)
      ->getQuery()->getResult();
    foreach ($changes as $change) {
      $this->deletedChanges[$change->getHierarchyEntity()->getId()][$ranking
        ->getId()][$change->getPlayer()->getPlayerId()] = $change;
      $this->entityManager->remove($change);
    }
  }

  /**
   * Gets the earliest influence for the given entity
   * @param RankingSystem $ranking the ranking system for which to get the influence
   * @param TournamentHierarchyInterface $entity the entity to analyze
   * @param bool $parentIsRanked true iff a predecessor contained the given ranking in its ranking systems
   * @return \DateTime|null the earliest influence or null if $parentIsRanked is false and the entity and all its
   *                        successors do not have the ranking in its ranking systems
   * @throws \App\Exceptions\ValueNotSet the ranking has no id
   */
  private function getEarliestEntityInfluence(RankingSystem $ranking, TournamentHierarchyInterface $entity,
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

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * Recomputes the given ranking list by using base as base list and applying the changes for the given entities
   * starting from the given index. If list is not the current list only the entities up to $list->getLastEntryTime()
   * are applied and the index gets changed accordingly.
   * @param RankingSystemList $list the list to recompute
   * @param RankingSystemList $base the list to use as base
   * @param TournamentHierarchyEntity[] $entities the list of entities to use for the computation
   * @param int $nextEntityIndex the first index in the entities list to consider
   * @throws \App\Exceptions\ValueNotSet at least one entry of base has no points or no player or a player with no id
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
      $changes = $this->getChanges($entities[$nextEntityIndex], $list);
      foreach ($changes as $change) {
        /** @noinspection PhpUnhandledExceptionInspection change comes from getChanges which produces changes with a
         * player which also has an id*/
        $entry = $this->getOrCreateRankingSystemListEntry($list, $change->getPlayer());
        $entry->setNumberRankedEntities($entry->getNumberRankedEntities() + 1);
        /** @noinspection PhpUnhandledExceptionInspection change comes from getChanges which produces changes with a
         * valid points change*/
        $pointsAfterwards = $entry->getPoints() + $change->getPointsChange();
        $entry->setPoints($pointsAfterwards);
        $change->setPointsAfterwards($pointsAfterwards);
        //apply further changes
        foreach ($this->getAdditionalFields() as $field => $value) {
          /** @noinspection PhpUnhandledExceptionInspection all fields from getAdditionalFields got set for an entry
           * from getOrCreateRankingSystemListEntry */
          $entry->setProperty($field, $entry->getProperty($field) + $change->getProperty($field));
        }
        if ($time > $list->getLastEntryTime()) {
          $list->setLastEntryTime($time);
        }
        $this->entityManager->persist($change);
      }
    }
    $nextEntityIndex = count($entities);
  }
//</editor-fold desc="Private Methods">
}