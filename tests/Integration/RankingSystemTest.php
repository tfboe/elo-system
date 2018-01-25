<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 8:47 AM
 */

namespace Tests\Integration;


use Tfboe\FmLib\Entity\RankingSystem;
use Tfboe\FmLib\Entity\Tournament;
use Tfboe\FmLib\Helpers\Level;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\AuthenticatedTestCase;

/**
 * Class RankingSystemServiceTest
 * @package Tests\Integration
 */
class RankingSystemTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testCreateTournamentWithEloRankings()
  {
    $players = $this->createPlayers(2);
    /** @var RankingSystem $ranking1 */
    $ranking1 = entity(RankingSystem::class)->create(['defaultForLevel' => Level::TOURNAMENT,
      'serviceName' => "EloRanking"]);
    /** @var RankingSystem $ranking2 */
    $ranking2 = entity(RankingSystem::class)->create(['defaultForLevel' => Level::COMPETITION,
      'serviceName' => "EloRanking"]);
    /** @var RankingSystem $ranking3 */
    $ranking3 = entity(RankingSystem::class)->create(['defaultForLevel' => Level::PHASE,
      'serviceName' => "EloRanking"]);
    /** @var RankingSystem $ranking4 */
    $ranking4 = entity(RankingSystem::class)->create(['defaultForLevel' => Level::MATCH,
      'serviceName' => "EloRanking"]);
    /** @var RankingSystem $ranking5 */
    $ranking5 = entity(RankingSystem::class)->create(['defaultForLevel' => Level::GAME,
      'serviceName' => "EloRanking"]);
    /** @var RankingSystem $ranking6 */
    $ranking6 = entity(RankingSystem::class)->create(['defaultForLevel' => Level::TOURNAMENT,
      'serviceName' => "EloRanking"]);

    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 1, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 1,
                  'resultB' => 0,
                  'result' => 'TEAM_A_WINS',
                  'played' => true,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ]
            ]
          ]
        ],
      ],
    ];
    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)->assertResponseOk();

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament $tournament */
    $tournament = $repo->findOneBy(['creator' => $this->user, 'userIdentifier' => 'id0']);
    self::assertEquals(
      [$ranking1->getId() => $ranking1, $ranking6->getId() => $ranking6],
      $tournament->getRankingSystems()->toArray());
    self::assertEquals(
      [$ranking2->getId() => $ranking2],
      $tournament->getCompetitions()['Test Competition']->getRankingSystems()->toArray());
    self::assertEquals(
      [$ranking3->getId() => $ranking3],
      $tournament->getCompetitions()['Test Competition']->getPhases()[1]->getRankingSystems()->toArray());
    self::assertEquals(
      [$ranking4->getId() => $ranking4],
      $tournament->getCompetitions()['Test Competition']->getPhases()[1]->getMatches()[1]
        ->getRankingSystems()->toArray());
    self::assertEquals(
      [$ranking5->getId() => $ranking5],
      $tournament->getCompetitions()['Test Competition']->getPhases()[1]->getMatches()[1]->getGames()[1]
        ->getRankingSystems()->toArray());
  }
//</editor-fold desc="Public Methods">
}