<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 8:45 PM
 */

namespace App\Http\Controllers;


use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\Player;
use App\Entity\Ranking;
use App\Entity\RankingSystemChange;
use App\Entity\RankingSystemListEntry;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\Tournament;
use App\Entity\User;
use App\Service\AsyncServices\RecalculateRankingSystemsInterface;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Query;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tfboe\FmLib\Entity\PlayerInterface;
use Tfboe\FmLib\Entity\RankingInterface;
use Tfboe\FmLib\Service\LoadingServiceInterface;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;


/**
 * Class RankingController
 * @package App\Http\Controllers
 */
class RankingController extends AsyncableController
{
//<editor-fold desc="Public Methods">

  /**
   * @param RankingSystemServiceInterface $rss
   * @return JsonResponse
   */
  public function rankings(Request $request): JsonResponse
  {
    $qb = $this->getEntityManager()->createQueryBuilder();
    $qb = $qb->from(RankingSystemListEntry::class, 'rse')
    ->select('rse.points AS points')
    ->addSelect('rse.numberRankedEntities AS nGames')
    ->addSelect('rse.subClassData AS subClassData')
    ->addSelect('p.firstName AS firstName')
    ->addSelect('p.lastName AS lastName')
    ->addSelect('p.id AS playerId')
    ->addSelect('GROUP_CONCAT(mp.id) AS mergedPlayerIds')
    ->innerJoin('rse.player', 'p')
    ->innerJoin('rse.rankingSystemList', 'l')
    ->leftJoin('p.mergedPlayers', 'mp')
    ->where('l.current = 1');
    if (array_key_exists("id", $request->input())) {
      $id = $request->input("id");
      $qb->andWhere($qb->expr()->eq('IDENTITY(l.rankingSystem)', ':id'))
        ->setParameter("id", $id);
    }
    if (array_key_exists("min_birth_date", $request->input())) {
      $minBirthDay = \DateTime::createFromFormat('Y-m-d', $request->input("min_birth_date"));
      $qb->andWhere($qb->expr()->gte('p.birthday', ':min_birth_day'))
      ->setParameter("min_birth_day", $minBirthDay);
    }
    $result = $qb
      ->groupBy('rse.id')
      ->getQuery()->getResult();
    $qb = $this->getEntityManager()->createQueryBuilder();
    $dateMinusThreeMonth = date("Y-m-d H:i:s", strtotime("-3 months"));
    $lastThreeMonthChanges = $qb->from(RankingSystemChange::class, 'c')
      ->select('SUM(c.pointsChange) AS change')
      ->addSelect('IDENTITY(c.player) AS player_id')
      ->innerJoin('c.hierarchyEntity', 'e')
      ->where($qb->expr()->gte('e.startTime', "'" . $dateMinusThreeMonth . "'"))
      ->andWhere('c.pointsChange < 1200')
      ->groupBy('player_id')
      ->getQuery()->getResult();

    $idMap = [];
    for ($i = 0; $i < count($result); $i++) {
      $idMap[$result[$i]['playerId']] = $i;
      $result[$i]['threeMonthChange'] = 0;
    }
    foreach ($lastThreeMonthChanges as $change) {
      if (array_key_exists($change['player_id'], $idMap)) {
        $result[$idMap[$change['player_id']]]['threeMonthChange'] = floatval($change['change']);
      }
    }


    return response()->json($result);
  }

  public function tournamentProfile(Request $request, string $rankingId, string $playerId, LoadingServiceInterface $ls): JsonResponse
  {
    //TODO What happens if associated hierarchyEntity is not a game?
    /** @var Player $player */
    $player = $this->getEntityManager()->find(Player::class, $playerId);
    $qb = $this->getEntityManager()->createQueryBuilder();
    $changes = $qb->from(RankingSystemChange::class, 'rsc')
      ->select('rsc')
      ->where($qb->expr()->eq('rsc.player', ':player'))
      ->setParameter('player', $player)
      ->andWhere($qb->expr()->eq('IDENTITY(rsc.rankingSystem)', ':rankingId'))
      ->setParameter('rankingId', $rankingId)
      ->getQuery()->setHint(\Doctrine\ORM\Query::HINT_INCLUDE_META_COLUMNS, true)
      ->getResult(Query::HYDRATE_ARRAY);
    // DO NOT HYDRATE AS OBJECT SINCE RankingSystemChange WILL SEARCH ALL GAMES SINGULARLY
    // (SEE Performance impact REMARK for Class Table Inheritance in
    // https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/inheritance-mapping.html#performance-impact)

    $changeIds = [];
    $gameIds = [];
    foreach ($changes as $change) {
      $changeIds[] = $change['id'];
      $gameIds[] = $change['hierarchy_entity_id'];
    }

    //get games
    $qb = $this->getEntityManager()->createQueryBuilder();
    /** @var Game[] $changes */
    $games = $qb->from(Game::class, 'e')
      ->select('e')
      ->where($qb->expr()->in('e.id', $gameIds))
      ->getQuery()->getResult();

    //load games completely
    $ls->loadEntities($games, [
      Game::class => [['match', 'playersA', 'playersB']],
      Match::class => [['phase', 'rankingsA', 'rankingsB']],
      Ranking::class => [['teams']],
      Team::class => [['memberships']],
      TeamMembership::class => [['player']],
      Phase::class => [['competition']],
      Competition::class => [['tournament']]
    ]);

    //load ranking system changes as objects
    $qb = $this->getEntityManager()->createQueryBuilder();
    /** @var RankingSystemChange[] $changes */
    $changes = $qb->from(RankingSystemChange::class, 'e')
      ->select('e')
      ->where($qb->expr()->in('e.id', $changeIds))
      ->getQuery()->getResult();

    $tournamentIdMap = [];
    $result = ["playerName" => ["firstName" => $player->getFirstName(), "lastName" => $player->getLastName()], "tournaments" => []];
    /** @var User|null $user */
    $user = $request->user();
    foreach ($changes as $change) {
      /** @var Game $game */
      $game = $change->getHierarchyEntity();
      $match = $game->getMatch();
      $phase = $match->getPhase();
      /** @var Tournament $tournament */
      $tournament = $phase->getCompetition()->getTournament();
      $isTeamA = $game->getPlayersA()->containsKey($player->getId());
      if (!array_key_exists($tournament->getId(), $tournamentIdMap)) {
        $tInfo = [];
        $tInfo['name'] = $tournament->getName();
        $tInfo['start'] = $tournament->getStartTime() === null ? null : $tournament->getStartTime()->getTimestamp();
        $team = $this->getTeam($isTeamA ? $match->getRankingsA() : $match->getRankingsB(), $player);
        $tInfo['ownRank'] = (!$tournament->isFinished()) ? null : ($team === null ? null : $team->getRank());
        $partner = $this->getPartnerFromTeam($team, $player);
        $tInfo['partner'] = $team->getName() ?:
          ($partner !== null ? $partner->getFirstName() . " " . $partner->getLastName() : null);

        if (!!$user && $tournament->getCreator()->getId() === $user->getId()) {
          $tInfo['localIdentifier'] = $tournament->getLocalIdentifier();
        }
        $tournamentIdMap[$tournament->getId()] = count($result["tournaments"]);
        $result["tournaments"][] = ["info" => $tInfo, "games" => []];
      }
      $info = [];
      $partner = $this->getPartner($isTeamA ? $game->getPlayersA() : $game->getPlayersB(), $player);
      $info['partner'] = $partner !== null ? ["firstName" => $partner->getFirstName(), "lastName" =>
        $partner->getLastName()] : null;

      $info['phaseName'] = $phase->getName();
      $info['result'] = $isTeamA ? ($game->getResultA() . ":" . $game->getResultB()) :
        ($game->getResultB() . ":" . $game->getResultA());
      $info['opponents'] = $this->getOpponent($isTeamA ? $game->getPlayersB() : $game->getPlayersA());
      $info['elo'] = $change->getPointsChange();
      $info['newElo'] = $change->getPointsAfterwards();
      $info['teamElo'] = $change->getTeamElo();
      $info['opponentElo'] = $change->getOpponentElo();
      $info['matchNumber'] = $match->getMatchNumber();
      $info['gameNumber'] = $game->getGameNumber();
      $info['phaseNumber'] = $phase->getPhaseNumber();
      $info['competitionIdentifier'] = $phase->getCompetition()->getLocalIdentifier();
      $info['competitionName'] = $phase->getCompetition()->getName();
      $info['start'] = $game->getStartTime() === null ? null : $game->getStartTime()->getTimestamp();
      $info['competitionRank'] = (!$tournament->isFinished()) ? null : ($team === null ? null : $team->getRank());
      $result["tournaments"][$tournamentIdMap[$tournament->getId()]]["games"][] = $info;
    }

    return response()->json($result);
  }

  public function recalculateRankings(Request $request): JsonResponse {
    return $this->checkAsync($request, RecalculateRankingSystemsInterface::class);
  }

  /**
   * @param Collection|Player[] $players
   * @return string[]
   */
  private function getOpponent(Collection $players): array
  {
    $result = [];
    foreach ($players as $player) {
      $result[] = ["firstName" => $player->getFirstName(), "lastName" => $player->getLastName()];
    }
    return $result;
  }

  /**
   * @param Collection|PlayerInterface[] $players
   * @param Player $player
   * @return Player|null
   */
  private function getPartner(Collection $players, Player $player): ?Player
  {
    if ($players->count() !== 2) {
      return null;
    }
    if ($players->first()->getId() === $player->getId()) {
      return $players->last();
    }
    if ($players->last()->getId() === $player->getId()) {
      return $players->first();
    }
    return null;
  }

  /**
   * @param Team $team
   * @param Player $player
   * @return Player|null
   */
  private function getPartnerFromTeam(Team $team, Player $player): ?Player
  {
    if ($team->getMemberships()->count() !== 2) {
      return null;
    }
    if ($team->getMemberships()->first()->getPlayer()->getId() === $player->getId()) {
      return $team->getMemberships()->last()->getPlayer();
    } else if ($team->getMemberships()->last()->getPlayer()->getId() === $player->getId()) {
      return $team->getMemberships()->first()->getPlayer();
    }
    return null;
  }


  /**
   * @param Collection|RankingInterface[] $rankings
   * @param Player $player
   * @return Team|null
   */
  private function getTeam(Collection $rankings, Player $player): ?Team
  {
    /** @var Team[] $possibleTeams */
    $possibleTeams = [];
    foreach ($rankings as $ranking) {
      foreach ($ranking->getTeams() as $team) {
        $possibleTeams[] = $team;
      }
    }
    if (count($possibleTeams) === 1) {
      return $possibleTeams[0];
    } else {
      foreach ($possibleTeams as $team) {
        foreach ($team->getMemberships() as $membership) {
          if ($membership->getPlayer()->getId() === $player->getId()) {
            return $team;
          }
        }
      }
    }
    return null;
  }
//</editor-fold desc="Public Methods">
}