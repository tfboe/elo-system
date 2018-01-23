<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 3/7/17
 * Time: 4:31 PM
 */

namespace App\Service\RankingSystem;

use App\Entity\Game;
use App\Entity\Helpers\Result;
use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Player;
use App\Entity\RankingSystem;
use App\Entity\RankingSystemChange;
use App\Entity\RankingSystemList;
use App\Entity\RankingSystemListEntry;
use Doctrine\Common\Collections\Collection;

/**
 * Class EloRanking
 * @package App\Service\TournamentRanking
 */
class EloRanking extends GameRankingSystemService implements EloRankingInterface
{
//<editor-fold desc="Fields">
  const EXP_DIFF = 400;
  const K = 20;
  const MAX_DIFF_TO_OPPONENT_FOR_PROVISORY = 400;
  const NO_NEG = true;
  const NUM_PROVISORY_GAMES = 20;
  const PROVISORY_PARTNER_FACTOR = 0.5;
  const START = 1200;
//</editor-fold desc="Fields">

  /**
   * @param Collection|Player[] $players
   */

//<editor-fold desc="Protected Methods">
  /**
   * Gets additional fields for this ranking type
   * @return string[] list of additional fields
   */
  protected function getAdditionalFields(): array
  {
    return ['playedGames' => 0, 'ratedGames' => 0, 'provisoryRanking' => 1200.0];
  }

  /**
   * @inheritDoc
   */
  protected function getChanges(TournamentHierarchyEntity $entity, RankingSystemList $list): array
  {
    /** @var Game $game */
    $game = $entity;
    $changes = [];

    if (!$game->isPlayed() || $game->getResult() === Result::NOT_YET_FINISHED ||
      $game->getResult() === Result::NULLED) {
      //game gets not elo rated
      $this->addNotRatedChanges($changes, $game->getPlayersA(), $entity, $list->getRankingSystem());
      $this->addNotRatedChanges($changes, $game->getPlayersB(), $entity, $list->getRankingSystem());
      return $changes;
    }

    $entriesA = $this->getEntriesOfPlayers($game->getPlayersA(), $list);
    $entriesB = $this->getEntriesOfPlayers($game->getPlayersB(), $list);

    $isAProvisory = $this->hasProvisoryEntry($entriesA);
    $isBProvisory = $this->hasProvisoryEntry($entriesB);

    $averageA = $this->getEloAverage($entriesA);
    $averageB = $this->getEloAverage($entriesB);

    $expectationA = 1 / (1 + 10 ** (($averageB - $averageA) / self::EXP_DIFF));
    $expectationB = 1 - $expectationA;

    $resultA = 0.0;

    switch ($game->getResult()) {
      case Result::TEAM_A_WINS:
        $resultA = 1.0;
        break;
      case Result::DRAW:
        $resultA = 0.5;
        break;
    }
    $resultB = 1 - $resultA;

    $expectationDiffA = $resultA - $expectationA;
    $expectationDiffB = $resultB - $expectationB;


    $this->computeChanges($changes, $entriesA, $resultA, $expectationDiffA, $game, $averageA, $averageB,
      $isAProvisory, $isBProvisory);
    $this->computeChanges($changes, $entriesB, $resultB, $expectationDiffB, $game, $averageB, $averageA,
      $isBProvisory, $isAProvisory);
    return $changes;
  }

  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * @inheritDoc
   */
  protected function startPoints(): float
  {
    return 1200.0;
  }
//</editor-fold desc="Protected Methods">


//<editor-fold desc="Private Methods">
  /**
   * @param RankingSystemChange[] $changes
   * @param Collection|Player[] $players
   * @param TournamentHierarchyEntity $entity
   * @param \App\Entity\RankingSystem $ranking
   * @throws \App\Exceptions\ValueNotSet either entity, ranking or at least one player has no id
   */
  private function addNotRatedChanges(array &$changes, Collection $players, TournamentHierarchyEntity $entity,
                                      RankingSystem $ranking)
  {
    foreach ($players as $player) {
      $change = $this->getOrCreateChange($entity, $ranking, $player);
      $change->setPointsChange(0.0);
      $change->setPlayedGames(0);
      $change->setRatedGames(0);
      $change->setProvisoryRanking(0.0);
      $changes[] = $change;
    }
  }

  /** @noinspection PhpTooManyParametersInspection */
  /**
   * @param array $changes
   * @param RankingSystemListEntry[] $entries
   * @param float $result
   * @param float $expectationDiff
   * @param Game $game
   * @param float $teamAverage
   * @param float $opponentAverage
   * @param bool $teamHasProvisory
   * @param bool $opponentHasProvisory
   * @throws \App\Exceptions\ValueNotSet at least one of the entries has no player or no ranking system list or its
   *                                     ranking system list has no ranking system
   */
  private function computeChanges(array &$changes, array $entries, float $result, float $expectationDiff, Game $game,
                                  float $teamAverage, float $opponentAverage, bool $teamHasProvisory,
                                  bool $opponentHasProvisory)
  {
    foreach ($entries as $entry) {
      $change = $this->getOrCreateChange($game, $entry->getRankingSystemList()->getRankingSystem(),
        $entry->getPlayer());
      $change->setPlayedGames(1);
      $factor = 2 * $result - 1;
      if ($entry->getPlayedGames() < self::NUM_PROVISORY_GAMES) {
        //provisory entry => recalculate
        if (count($entries) > 1) {
          $teamMatesAverage = ($teamAverage * count($entries) - $entry->getProvisoryRanking()) /
            (count($entries) - 1);
          if ($teamMatesAverage > $opponentAverage + self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY) {
            $teamMatesAverage = $opponentAverage + self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY;
          }
          if ($teamMatesAverage < $opponentAverage - self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY) {
            $teamMatesAverage = $opponentAverage - self::MAX_DIFF_TO_OPPONENT_FOR_PROVISORY;
          }
          $performance = $opponentAverage * (1 + self::PROVISORY_PARTNER_FACTOR) -
            $teamMatesAverage * self::PROVISORY_PARTNER_FACTOR;
        } else {
          $performance = $opponentAverage;
        }
        if ($performance < self::START) {
          $performance = self::START;
        }
        $performance += self::EXP_DIFF * $factor;
        //old average performance = $entry->getProvisoryRating()
        //=> new average performance = ($entry->getProvisoryRating() * $entry->getRatedGames() + $performance) /
        //                             ($entry->getRatedGames() + 1)
        //=> performance change = ($entry->getProvisoryRating() * $entry->getRatedGames() + $performance) /
        //                        ($entry->getRatedGames() + 1) - $entry->getProvisoryRating()
        //                      = ($performance - $entry->getProvisoryRating()) / ($entry->getRatedGames() + 1)
        $change->setProvisoryRanking(($performance - $entry->getProvisoryRanking()) / ($entry->getRatedGames() + 1));
        $change->setPointsChange(0.0);
        $change->setRatedGames(1);
      } else if (!$teamHasProvisory && !$opponentHasProvisory) {
        //real elo ranking
        $change->setProvisoryRanking(0.0);
        $change->setPointsChange(self::K * $expectationDiff);
        $change->setRatedGames(1);
      } else {
        //does not get rated
        $change->setProvisoryRanking(0.0);
        $change->setPointsChange(0.0);
        $change->setRatedGames(0);
      }
      $changes[] = $change;
    }
  }

  /**
   * Computes the average rating of the given entries
   * @param RankingSystemListEntry[] $entries must be nonempty
   * @return float
   * @throws \App\Exceptions\ValueNotSet at least one of the entries has no points set
   */
  private function getEloAverage(array $entries): float
  {
    $sum = 0;
    foreach ($entries as $entry) {
      $sum += $entry->getRatedGames() < self::NUM_PROVISORY_GAMES ? $entry->getProvisoryRanking() : $entry->getPoints();
    }
    return $sum / count($entries);
  }

  /**
   * Checks if the given list of entries has at least one provisory entry
   * @param RankingSystemListEntry[] $entries
   * @return bool
   */
  private function hasProvisoryEntry(array $entries): bool
  {
    foreach ($entries as $entry) {
      if ($entry->getPlayedGames() < self::NUM_PROVISORY_GAMES) {
        return true;
      }
    }
    return false;
  }
//</editor-fold desc="Private Methods">
}