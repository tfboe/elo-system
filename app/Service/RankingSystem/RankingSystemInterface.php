<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:36 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Tournament;

/**
 * Interface RankingSystemInterface
 * @package App\Service\RankingSystem
 */
interface RankingSystemInterface
{
//<editor-fold desc="Public Methods">
  /**
   * The earliest influence date of the given tournament for the ranking system.
   * This method must be called before a tournament changes and the result must then be used for the
   * earliest_old_influence parameter in the updateRankingForTournament method.
   * @param \App\Entity\RankingSystem $ranking
   * @param Tournament $tournament
   * @return \DateTime|null the earliest influence or null iff no entity above the level of this ranking has the ranking
   *                        in its rankingSystems
   */
  public function getEarliestInfluence(\App\Entity\RankingSystem $ranking, Tournament $tournament): ?\DateTime;

  /**
   * Updates the rankings for this tournament
   * @param \App\Entity\RankingSystem $ranking
   * @param Tournament $tournament
   * @param \DateTime|null $earliest_old_influence if the tournament changed this is the earliest influence of the
   *                       tournament before the change
   */
  public function updateRankingForTournament(\App\Entity\RankingSystem $ranking, Tournament $tournament,
                                             ?\DateTime $earliest_old_influence): void;

  /**
   * Updates the rankings assuming all changes happened after $from.
   * @param \App\Entity\RankingSystem $ranking
   * @param \DateTime $from
   */
  public function updateRankingFrom(\App\Entity\RankingSystem $ranking, \DateTime $from): void;
//</editor-fold desc="Public Methods">
}