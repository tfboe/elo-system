<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 3/7/17
 * Time: 4:31 PM
 */

namespace App\Service\RankingSystem;

/**
 * Class EloRanking
 * @package App\Service\TournamentRanking
 */
class EloRanking extends GameRankingSystem implements EloRankingInterface
{
//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function updateRankingFrom(\App\Entity\RankingSystem $ranking, \DateTime $from): void
  {
    // TODO: Implement updateRankingFrom() method.
  }
//</editor-fold desc="Public Methods">
}