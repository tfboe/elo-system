<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/4/18
 * Time: 4:10 PM
 */

namespace App\Service;


use App\Entity\Tournament;

/**
 * Interface RankingSystemServiceInterface
 * @package App\Service
 */
interface RankingSystemServiceInterface
{
//<editor-fold desc="Public Methods">
  /**
   * Applies all changes of a tournament to all relevant ranking systems.
   * @param Tournament $tournament The tournament to analyze
   * @param mixed[][] $earliest_influences the earliest influences of the tournament before the change
   */
  public function applyRankingSystems(Tournament $tournament, array $earliest_influences): void;

  /**
   * Gets all ranking systems of a tournament and its earliest influences as time.
   * The result is used as input for the method applyRankingSystems.
   * @param Tournament $tournament the tournament to analyze
   * @return mixed[][]
   */
  public function getRankingSystemsEarliestInfluences(Tournament $tournament): array;
//</editor-fold desc="Public Methods">
}