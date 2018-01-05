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
use App\Entity\Tournament;


/**
 * Class RankingSystem
 * @package App\Service\RankingSystem
 */
abstract class RankingSystem implements RankingSystemInterface
{
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
   * @inheritdoc
   */
  public abstract function updateRankingFrom(\App\Entity\RankingSystem $ranking, \DateTime $from): void;
//</editor-fold desc="Public Methods">

//<editor-fold desc="Protected Methods">
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
//</editor-fold desc="Private Methods">
}