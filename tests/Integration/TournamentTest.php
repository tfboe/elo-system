<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 8:48 PM
 */

namespace Tests\Integration;

use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\ApplicationGetter;
use Tfboe\FmLib\Entity\Categories\GameMode;
use Tfboe\FmLib\Entity\Categories\OrganizingMode;
use Tfboe\FmLib\Entity\Categories\ScoreMode;
use Tfboe\FmLib\Entity\Categories\Table;
use Tfboe\FmLib\Entity\Categories\TeamMode;
use Tfboe\FmLib\Entity\Competition;
use Tfboe\FmLib\Entity\Game;
use Tfboe\FmLib\Entity\Helpers\Result;
use Tfboe\FmLib\Entity\Match;
use Tfboe\FmLib\Entity\Phase;
use Tfboe\FmLib\Entity\Player;
use Tfboe\FmLib\Entity\QualificationSystem;
use Tfboe\FmLib\Entity\Ranking;
use Tfboe\FmLib\Entity\Team;
use Tfboe\FmLib\Entity\Tournament;
use Tfboe\FmLib\TestHelpers\AuthenticatedTestCase;

/**
 * Class TournamentTest
 * @package Tests\Integration
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TournamentTest extends AuthenticatedTestCase
{
  use ApplicationGetter;
//<editor-fold desc="Public Methods">

  /**
   * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
   */
  public function testCreateTournamentFull()
  {
    $players = $this->createPlayers(5);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'tournamentListId' => 'testList',
      'gameMode' => 'SPEEDBALL',
      'organizingMode' => 'ELIMINATION',
      'scoreMode' => 'BEST_OF_FIVE',
      'teamMode' => 'DOUBLE',
      'table' => 'ROBERTO_SPORT',
      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'gameMode' => 'CLASSIC',
          'organizingMode' => 'QUALIFICATION',
          'scoreMode' => 'ONE_SET',
          'teamMode' => 'SINGLE',
          'table' => 'MULTITABLE',
          'teams' => [
            ['name' => 'Team 1', 'rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['name' => 'Team 2', 'rank' => 1, 'startNumber' => 3, 'players' => [$players[1]->getPlayerId(),
              $players[2]->getPlayerId()]],
            ['name' => 'Team 3', 'rank' => 4, 'startNumber' => 2, 'players' =>
              [$players[2]->getPlayerId(), $players[3]->getPlayerId(), $players[4]->getPlayerId()]]
          ],
          'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
          'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
          'phases' => [
            [
              'name' => 'Phase 1',
              'phaseNumber' => 1,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_THREE',
              'teamMode' => 'DYP',
              'table' => 'BONZINI',
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 2, 'teamStartNumbers' => [3], 'uniqueRank' => 2],
                ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 3],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 1,
                  'resultB' => 0,
                  'result' => 'TEAM_A_WINS',
                  'played' => true,
                  'startTime' => '2017-12-19 15:00:31 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ],
                [
                  'matchNumber' => 2,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [3],
                  'resultA' => 0,
                  'resultB' => 1,
                  'result' => 'TEAM_B_WINS',
                  'played' => true,
                  'startTime' => '2017-12-19 16:00:00 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[4]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[3]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ],
                [
                  'matchNumber' => 3,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [3],
                  'resultA' => 1,
                  'resultB' => 1,
                  'result' => 'DRAW',
                  'played' => true,
                  'startTime' => '2017-12-19 16:00:00 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[1]->getPlayerId()],
                      'playersB' => [$players[2]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[1]->getPlayerId()],
                      'playersB' => [$players[4]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [2],
            ],
            [
              'name' => 'Phase 2',
              'phaseNumber' => 2,
              'gameMode' => 'SPEEDBALL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_FIVE',
              'teamMode' => 'DOUBLE',
              'table' => 'GARLANDO',
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [3], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'NULLED',
                  'played' => true,
                  'startTime' => '2017-12-19 15:00:31 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[3]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[2]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [3, 4],
            ],
            [
              'name' => 'Phase 3',
              'phaseNumber' => 3,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_THREE',
              'teamMode' => 'DYP',
              'table' => 'LEONHART',
              'rankings' => [
                ['rank' => 3, 'teamStartNumbers' => [3], 'uniqueRank' => 1], //is this allowed???
                ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 3],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 7,
                  'resultB' => 0,
                  'result' => 'TEAM_A_WINS',
                  'played' => false,
                  'startTime' => '2017-12-19 15:00:31 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[4]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[2]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [5, 6],
            ],
            [
              'name' => 'Phase 4',
              'phaseNumber' => 4,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_THREE',
              'teamMode' => 'DYP',
              'table' => 'LEONHART',
              'rankings' => [
                ['rank' => 3, 'teamStartNumbers' => [3], 'uniqueRank' => 1], //is this allowed???
                ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 3],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'DRAW',
                  'played' => false,
                  'startTime' => '2017-12-19 15:00:31 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[4]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[3]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [5],
            ],
            [
              'name' => 'Phase 5',
              'phaseNumber' => 5,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_THREE',
              'teamMode' => 'DYP',
              'table' => 'LEONHART',
              'rankings' => [
                ['rank' => 3, 'teamStartNumbers' => [3], 'uniqueRank' => 1], //is this allowed???
                ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 3],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'NULLED',
                  'played' => false,
                  'startTime' => '2017-12-19 15:00:31 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[2]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[3]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [6],
            ],
            [
              'name' => 'Phase 6',
              'phaseNumber' => 6,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_THREE',
              'teamMode' => 'DYP',
              'table' => 'LEONHART',
              'rankings' => [
                ['rank' => 3, 'teamStartNumbers' => [3], 'uniqueRank' => 1], //is this allowed???
                ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 3],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 5,
                  'resultB' => 5,
                  'result' => 'DRAW',
                  'played' => true,
                  'startTime' => '2017-12-19 17:00:05 Europe/Vienna', //is this allowed???
                  'endTime' => '2017-12-19 16:00:00 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[4]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[2]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [],
            ],
          ]
        ],
        [
          'name' => 'Another Competition',
          'gameMode' => 'CLASSIC',
          'organizingMode' => 'ELIMINATION',
          'scoreMode' => 'BEST_OF_THREE',
          'teamMode' => 'DYP',
          'table' => 'GARLANDO',
          'teams' => [
            ['name' => 'Team 1', 'rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['name' => 'Team 2', 'rank' => 1, 'startNumber' => 3, 'players' => [$players[1]->getPlayerId()]],
            ['name' => 'Team 3', 'rank' => 4, 'startNumber' => 2, 'players' => [$players[2]->getPlayerId()]]
          ],
          'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
          'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
          'phases' => [
            [
              'name' => 'Phase 1',
              'phaseNumber' => 1,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_FIVE',
              'teamMode' => 'DOUBLE',
              'table' => 'TORNADO',
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
                ['rank' => 2, 'teamStartNumbers' => [3], 'uniqueRank' => 3],
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'NULLED',
                  'played' => false,
                  'startTime' => '2017-12-19 15:00:31 Europe/Vienna',
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna',
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'QUALIFICATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'GARLANDO',
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[1]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ],
                    [
                      'gameNumber' => 2,
                      'playersA' => [$players[0]->getPlayerId()],
                      'playersB' => [$players[2]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 1,
                      'result' => 'DRAW',
                      'played' => false,
                      'gameMode' => 'SPEEDBALL',
                      'organizingMode' => 'QUALIFICATION',
                      'scoreMode' => 'BEST_OF_FIVE',
                      'teamMode' => 'DOUBLE',
                      'table' => 'GARLANDO',
                      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
                      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [],
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)->seeJsonEquals(['type' => 'create']);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament $tournament */
    $tournament = $repo->findOneBy(['creator' => $this->user, 'userIdentifier' => 'id0']);

    $this->assertTournamentInfoByRequest($request, $tournament);
  }

  public function testCreateTournamentMin()
  {
    $players = $this->createPlayers(2);
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
    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)->seeJsonEquals(['type' => 'create']);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament $tournament */
    $tournament = $repo->findOneBy(['creator' => $this->user, 'userIdentifier' => 'id0']);

    $this->assertTournamentInfoByRequest($request, $tournament);
  }

  public function testDuplicateGameNumberException()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
                    ],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the game list of match 1 of phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateMatchNumber()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
                ],
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 5,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$players[1]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ],
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the match list of phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateNextPhaseNumber()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'nextPhaseNumbers' => [2, 2],
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
            ],
            [
              'phaseNumber' => 2,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                ['rank' => 2, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
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
                      'playersA' => [$players[1]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 2,
        "arrayName" => "the next phase numbers list of phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicatePhaseNumber()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
            ],
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                ['rank' => 2, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the phase list of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicatePlayerIdInGamePlayersA()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
                      'playersA' => [$players[0]->getPlayerId(), $players[0]->getPlayerId()],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception",
        "duplicateValue" => $players[0]->getPlayerId(),
        "arrayName" => "the players A list of the game with game number 1 of the match with match number 1 of the " .
          "phase 1 of the competition Test Competition", "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicatePlayerIdInGamePlayersAAndB()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
                      'playersB' => [$players[0]->getPlayerId(), $players[1]->getPlayerId()],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception",
        "duplicateValue" => $players[0]->getPlayerId(), "status" => 409,
        "arrayName" => "the players A and players B lists of the game with game number 1 of the match with match " .
          "number 1 of the phase 1 of the competition Test Competition", "name" => "DuplicateException"]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicatePlayerInTeam()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['name' => 'duplicate team', 'rank' => 1, 'startNumber' => 1,
              'players' => [$players[0]->getPlayerId(), $players[0]->getPlayerId()]],
            ['name' => 'other team', 'rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
            ],
          ]
        ]
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception",
        "duplicateValue" => $players[0]->getPlayerId(), "arrayName" => "the player list of team duplicate team",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateRankInMatchRankingsA()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1, 1],
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
                ],
              ],
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the rankings A list of the match with match number 1 of the phase 1 of the competition Test " .
          "Competition", "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateRankInMatchRankingsAAndB()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [1],
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
                ],
              ],
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the rankings A and ranking B lists of the match with match number 1 of the phase 1 of the " .
          "competition Test Competition", "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateRankingTeamStartNumber()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1, 1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the team list of ranking (Unique rank 1) of the phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateRankingUniqueRank()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
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
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the ranking list of phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateStartNumber()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 1, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'groupNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
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
            ],
          ]
        ]
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the team list of competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateTeamStartNumbersInRankings()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
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
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the team start number lists of the ranking of the phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testMatchRankingReferenceException()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [3],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Reference Exception", "referenceValue" => 3,
        "referenceName" => "unique rank in phase 1 of the competition Test Competition",
        "name" => "ReferenceException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testNextPhaseNumberReferenceException()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'nextPhaseNumbers' => [2],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Reference Exception", "referenceValue" => 2,
        "referenceName" => "phase numbers in competition Test Competition",
        "name" => "ReferenceException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testPlayerIdReferenceException()
  {
    $players = $this->createPlayers(3);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
            ['rank' => 3, 'startNumber' => 3, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                ['rank' => 1, 'teamStartNumbers' => [3], 'uniqueRank' => 3],
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
                      'playersB' => [$players[2]->getPlayerId()],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Reference Exception",
        "referenceValue" => $players[2]->getPlayerId(),
        "referenceName" => "player id in players B in game 1 of match 1 of phase 1 of competition Test Competition, " .
          "which is not in the players lists of the teams of team B", "name" => "ReferenceException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testTeamStartNumberReferenceException()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [3], 'uniqueRank' => 2],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Reference Exception", "referenceValue" => 3,
        "referenceName" => "team start number in competition Test Competition",
        "name" => "ReferenceException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  /**
   * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
   */
  public function testTournamentUpdate()
  {
    /** @var Tournament $tournament */
    $tournament = entity(Tournament::class)->create([
      'userIdentifier' => 't1',
      'creator' => $this->user,
      'gameMode' => GameMode::CLASSIC,
      'startTime' => new \DateTime('2017-12-30 15:00', new \DateTimeZone('Europe/Vienna')),
      'endTime' => new \DateTime('2017-12-31 18:35', new \DateTimeZone('Europe/Vienna')),
    ]);
    /** @var Competition[] $competitions */
    $competitions = [
      entity(Competition::class)->create(['name' => 'Test Competition']),
      entity(Competition::class)->create(['name' => 'Test Competition 2',
        'startTime' => new \DateTime('2017-12-30 15:00', new \DateTimeZone('Europe/Vienna')),
        'endTime' => new \DateTime('2017-12-31 18:35', new \DateTimeZone('Europe/Vienna'))]),
      entity(Competition::class)->create(['name' => 'Test Competition 4']),
      entity(Competition::class)->create(['name' => 'Test Competition 5'])];
    foreach ($competitions as $competition) {
      $competition->setTournament($tournament);
    }

    /** @var Team[][] $teams */
    $teams = [];
    $teams[0] = $this->createTeams(4);
    $teams[1] = $this->createTeams(4, 3);
    $teams[2] = $this->createTeams(3);
    $teams[3] = $this->createTeams(3, 2);

    /** @var Phase[][] $phases */
    $phases = [];
    $phases[0] = $this->createPhases(1, ['Main Phase']);
    $phases[1] = $this->createPhases(4, ['P1', 'P2', 'P3', 'P4'], [0 => [1, 2, 3], 2 => [3]]);
    $phases[2] = $this->createPhases(1);
    $phases[3] = $this->createPhases(1);

    /** @var Ranking[][][] $rankings */
    $rankings = [];
    $rankings[0] = [[
      $this->createRanking([$teams[0][0]], 1),
      $this->createRanking([$teams[0][1]], 2),
      $this->createRanking([$teams[0][2]], 3),
      $this->createRanking([$teams[0][3]], 3, 4)
    ]];
    $rankings[1] = [
      [
        $this->createRanking([$teams[1][0]], 1),
        $this->createRanking([$teams[1][1]], 2),
      ],
      [
        $this->createRanking([$teams[1][0]], 1),
        $this->createRanking([$teams[1][1]], 2),
        $this->createRanking([$teams[1][2]], 3),
        $this->createRanking([$teams[1][3]], 4)
      ],
      [
        $this->createRanking([$teams[1][0]], 1),
        $this->createRanking([$teams[1][1]], 2),
      ],
      [
        $this->createRanking([$teams[1][2]], 1),
        $this->createRanking([$teams[1][3]], 2),
      ],
    ];
    $rankings[2] = [[
      $this->createRanking([$teams[2][0]], 1),
      $this->createRanking([$teams[2][1]], 2),
    ]];
    $rankings[3] = [[
      $this->createRanking([$teams[3][0]], 1),
      $this->createRanking([$teams[3][1]], 2),
      $this->createRanking([$teams[3][2]], 2, 3),
    ]];

    /** @var Match[][][] $matches */
    $matches = [];
    $matches[0] = [[
      $this->createMatch([$rankings[0][0][0]], [$rankings[0][0][1]], 1, 7, 5,
        Result::TEAM_A_WINS, true, new \DateTime('2017-12-31 15:00', new \DateTimeZone('Europe/Vienna')),
        new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna'))),
      $this->createMatch([$rankings[0][0][2]], [$rankings[0][0][3]], 2)
    ]];
    $matches[1] = [
      [$this->createMatch([$rankings[1][0][0]], [$rankings[1][0][1]], 1)],
      [
        $this->createMatch([$rankings[1][1][0]], [$rankings[1][1][1]], 1),
        $this->createMatch([$rankings[1][1][2]], [$rankings[1][1][3]], 2)
      ],
      [$this->createMatch([$rankings[1][2][0]], [$rankings[1][2][1]], 1)],
      [$this->createMatch([$rankings[1][3][0]], [$rankings[1][3][1]], 1)]
    ];
    $matches[2] = [[
      $this->createMatch([$rankings[2][0][0]], [$rankings[2][0][1]], 1),
      $this->createMatch([$rankings[2][0][1]], [$rankings[2][0][0]], 2)
    ]];
    $matches[3] = [[
      $this->createMatch([$rankings[3][0][0]], [$rankings[3][0][1]], 1),
      $this->createMatch([$rankings[3][0][0]], [$rankings[3][0][1]], 2)
    ]];

    /** @var Game[][][][] $games */
    $games = [];
    $games[0] = [[
      [$this->createGame([$teams[0][0]->getPlayers()->first()], [$teams[0][1]->getPlayers()->first()], 1, 7, 5,
        Result::TEAM_A_WINS, true, new \DateTime('2017-12-31 15:00', new \DateTimeZone('Europe/Vienna')),
        new \DateTime('2017-12-31 16:00', new \DateTimeZone('Europe/Vienna')))],
      [$this->createGame([$teams[0][0]->getPlayers()->first()], [$teams[0][1]->getPlayers()->first()], 1)]
    ]];
    $games[1] = [
      [[$this->createGame([$teams[1][0]->getPlayers()->first()], [$teams[1][1]->getPlayers()->first()], 1)]],
      [
        [$this->createGame([$teams[1][0]->getPlayers()->first()], [$teams[1][1]->getPlayers()->first()], 1)],
        [$this->createGame([$teams[1][2]->getPlayers()->first()], [$teams[1][3]->getPlayers()->first()], 1)]
      ],
      [[
        $this->createGame([$teams[1][0]->getPlayers()->first()], [$teams[1][1]->getPlayers()->first()], 1),
        $this->createGame([$teams[1][0]->getPlayers()->first()], [$teams[1][1]->getPlayers()->first()], 2),
      ]],
      [[$this->createGame([$teams[1][0]->getPlayers()->first()], [$teams[1][1]->getPlayers()->first()], 1)]]
    ];
    $games[2] = [[
      [$this->createGame([$teams[2][0]->getPlayers()->first()], [$teams[2][1]->getPlayers()->first()], 1)],
      [$this->createGame([$teams[2][1]->getPlayers()->first()], [$teams[2][0]->getPlayers()->first()], 1)]
    ]];
    $games[3] = [[
      [$this->createGame([$teams[3][0]->getPlayers()->first()], [$teams[3][1]->getPlayers()->first()], 1)],
      [$this->createGame([$teams[3][0]->getPlayers()->first()], [$teams[3][1]->getPlayers()->first()], 1)]
    ]];


    for ($i = 0; $i < count($phases); $i++) {
      foreach ($teams[$i] as $team) {
        $team->setCompetition($competitions[$i]);
      }
      for ($j = 0; $j < count($phases[$i]); $j++) {
        $phase = $phases[$i][$j];
        $phase->setCompetition($competitions[$i]);
        for ($k = 0; $k < count($rankings[$i][$j]); $k++) {
          $rankings[$i][$j][$k]->setPhase($phase);
        }
        for ($k = 0; $k < count($matches[$i][$j]); $k++) {
          $matches[$i][$j][$k]->setPhase($phase);
          for ($l = 0; $l < count($games[$i][$j][$k]); $l++) {
            $games[$i][$j][$k][$l]->setMatch($matches[$i][$j][$k]);
          }
        }
      }
    }

    self::assertEquals('t1', $tournament->getUserIdentifier());
    self::assertEquals('', $tournament->getTournamentListId());
    self::assertEquals(4, $tournament->getCompetitions()->count());
    self::assertEquals(['Test Competition', 'Test Competition 2', 'Test Competition 4', 'Test Competition 5'],
      $tournament->getCompetitions()->getKeys());
    self::assertEquals(GameMode::CLASSIC, $tournament->getGameMode());
    self::assertNull($tournament->getOrganizingMode());
    self::assertNull($tournament->getScoreMode());
    self::assertNull($tournament->getTeamMode());
    self::assertNull($tournament->getTable());

    $request = [
      'name' => 'New Name',
      'userIdentifier' => 't1',
      'gameMode' => 'OFFICIAL',
      'table' => 'GARLANDO',
      'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
      'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 4, 'startNumber' => 1, 'players' => [$teams[0][0]->getPlayers()->first()->getPlayerId()]],
            ['rank' => 3, 'startNumber' => 2, 'players' => [$teams[0][1]->getPlayers()->first()->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 3, 'players' => [$teams[0][2]->getPlayers()->first()->getPlayerId()]],
            ['rank' => 1, 'startNumber' => 4, 'players' => [$teams[0][3]->getPlayers()->first()->getPlayerId()]],
          ],
          'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
          'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
          'phases' => [
            [
              'phaseNumber' => 1,
              'name' => 'Ph1',
              'rankings' => [
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 2, 'teamStartNumbers' => [2]]
              ],
              'startTime' => '2017-12-19 15:01:31 Europe/Vienna',
              'endTime' => '2017-12-19 16:01:05 Europe/Vienna',
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 2,
                      'playersA' => [$teams[0][1]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[0][0]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ]
            ],
            [
              'phaseNumber' => 2,
              'name' => 'Ph2',
              'rankings' => [
                ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [4]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$teams[0][3]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[0][0]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ],
                [
                  'matchNumber' => 2,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 5,
                  'resultB' => 5,
                  'result' => 'DRAW',
                  'played' => true,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$teams[0][0]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[0][3]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ]
            ],
          ]
        ],
        [
          'name' => 'Test Competition 2',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [
              $teams[1][0]->getPlayers()[0]->getPlayerId(),
              $teams[1][0]->getPlayers()[2]->getPlayerId(),
              $teams[1][1]->getPlayers()[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [
              $teams[1][1]->getPlayers()[1]->getPlayerId(),
              $teams[1][1]->getPlayers()[2]->getPlayerId()]
            ],
            ['rank' => 2, 'startNumber' => 3, 'players' => [
              $teams[1][2]->getPlayers()[0]->getPlayerId()]
            ],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'name' => 'P1',
              'rankings' => [
                ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$teams[1][2]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[1][0]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [2, 5]
            ],
            [
              'phaseNumber' => 2,
              'name' => 'Ph2',
              'rankings' => [
                ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$teams[1][2]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[1][0]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ],
              'nextPhaseNumbers' => [5]
            ],
            [
              'phaseNumber' => 4,
              'name' => 'P4',
              'rankings' => [
                ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]],
                ['rank' => 3, 'uniqueRank' => 3, 'teamStartNumbers' => [2]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$teams[1][2]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[1][1]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ],
            ],
            [
              'phaseNumber' => 5,
              'name' => 'P5',
              'rankings' => [
                ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]],
                ['rank' => 3, 'uniqueRank' => 3, 'teamStartNumbers' => [2]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 1,
                      'playersA' => [$teams[1][2]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[1][1]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ],
            ],
          ]
        ],
        [
          'name' => 'Test Competition 3',
          'teams' => [
            ['rank' => 3, 'startNumber' => 1, 'players' => [$teams[2][0]->getPlayers()->first()->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$teams[2][1]->getPlayers()->first()->getPlayerId()]],
            ['rank' => 1, 'startNumber' => 3, 'players' => [$teams[2][2]->getPlayers()->first()->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]],
                ['rank' => 3, 'uniqueRank' => 3, 'teamStartNumbers' => [2]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [1],
                  'rankingsBUniqueRanks' => [2],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
                  'games' => [
                    [
                      'gameNumber' => 2,
                      'playersA' => [$teams[2][2]->getPlayers()->first()->getPlayerId()],
                      'playersB' => [$teams[2][0]->getPlayers()->first()->getPlayerId()],
                      'resultA' => 1,
                      'resultB' => 0,
                      'result' => 'TEAM_A_WINS',
                      'played' => true,
                    ]
                  ]
                ]
              ],
            ],
          ]
        ],
      ],
    ];
    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)->seeJsonEquals(['type' => 'replace']);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    /** @var Tournament[] $tournaments */
    $tournaments = $repo->findAll();
    self::assertEquals(1, count($tournaments));
    $newTournament = $tournaments[0];
    self::assertEquals($tournament, $newTournament);
    $this->assertTournamentInfoByRequest($request, $tournament);
  }

  public function testUnorderedTestNumberException()
  {
    $players = $this->createPlayers(2);
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getPlayerId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getPlayerId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 2,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
              ],
              'nextPhaseNumbers' => [1],
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
            ],
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                ['rank' => 2, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
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
                      'playersA' => [$players[1]->getPlayerId()],
                      'playersB' => [$players[0]->getPlayerId()],
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

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Unordered Phase Number Exception",
        "nextPhaseNumber" => 1, "previousPhaseNumber" => 2,
        "name" => "UnorderedPhaseNumberException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @param mixed[] $data
   * @param Tournament $tournament
   * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
   * @SuppressWarnings(PHPMD.NPathComplexity)
   * @SuppressWarnings(PHPMD.CyclomaticComplexity)
   */
  private function assertTournamentInfoByRequest(array $data, Tournament $tournament)
  {
    $categories = [
      'gameMode' => ['default' => null, 'enum' => GameMode::class],
      'organizingMode' => ['default' => null, 'enum' => OrganizingMode::class],
      'scoreMode' => ['default' => null, 'enum' => ScoreMode::class],
      'teamMode' => ['default' => null, 'enum' => TeamMode::class],
      'table' => ['default' => null, 'enum' => Table::class],
    ];
    $dateTransformer = function ($dateString) {
      return \DateTime::createFromFormat('Y-m-d H:i:s e', $dateString);
    };
    $this->checkProperties($data, $tournament, [
      'name' => null,
      'tournamentListId' => '',
      'startTime' => ['transformer' => $dateTransformer, 'default' => null],
      'endTime' => ['transformer' => $dateTransformer, 'default' => null],
    ], $categories);
    self::assertInstanceOf(Collection::class, $tournament->getCompetitions());
    self::assertEquals(count($data['competitions']), $tournament->getCompetitions()->count());
    self::assertNotEmpty($tournament->getId());

    foreach ($data['competitions'] as $competitionData) {
      self::assertTrue($tournament->getCompetitions()->containsKey($competitionData['name']));
      /** @var Competition $competition */
      $competition = $tournament->getCompetitions()->get($competitionData['name']);
      $this->checkProperties($competitionData, $competition, [
        'name' => null,
        'startTime' => ['transformer' => $dateTransformer, 'default' => null],
        'endTime' => ['transformer' => $dateTransformer, 'default' => null],
      ], $categories);
      self::assertInstanceOf(Collection::class, $competition->getTeams());
      self::assertEquals(count($competitionData['teams']), $competition->getTeams()->count());
      foreach ($competitionData['teams'] as $teamData) {
        self::assertTrue($competition->getTeams()->containsKey($teamData['startNumber']));
        /** @var Team $team */
        $team = $competition->getTeams()->get($teamData['startNumber']);
        $this->checkProperties($teamData, $team, [
          'name' => null,
          'rank' => null,
          'startNumber' => null
        ]);
        self::assertInstanceOf(Collection::class, $team->getPlayers());
        self::assertEquals(count($teamData['players']), $team->getPlayers()->count());
        foreach ($teamData['players'] as $id) {
          $exists = false;
          foreach ($team->getPlayers() as $player) {
            if ($player->getPlayerId() == $id) {
              $exists = true;
              break;
            }
          }
          self::assertTrue($exists);
        }
      }

      foreach ($competitionData['phases'] as $phaseData) {
        self::assertTrue($competition->getPhases()->containsKey($phaseData['phaseNumber']));
        /** @var Phase $phase */
        $phase = $competition->getPhases()->get($phaseData['phaseNumber']);

        $this->checkProperties($phaseData, $phase, [
          'name' => '',
          'phaseNumber' => null,
          'startTime' => ['transformer' => $dateTransformer, 'default' => null],
          'endTime' => ['transformer' => $dateTransformer, 'default' => null],
        ], $categories);

        self::assertInstanceOf(Collection::class, $phase->getPostQualifications());
        if (array_key_exists('nextPhaseNumbers', $phaseData)) {
          self::assertEquals(count($phaseData['nextPhaseNumbers']), $phase->getPostQualifications()->count());
          foreach ($phaseData['nextPhaseNumbers'] as $phaseNumber) {
            $found = false;
            foreach ($phase->getPostQualifications() as $system) {
              if ($system->getNextPhase()->getPhaseNumber() === $phaseNumber) {
                $found = true;
                break;
              }
            }
            self::assertTrue($found);
          }
        } else {
          self::assertEquals(0, $phase->getPostQualifications()->count());
        }

        self::assertInstanceOf(Collection::class, $phase->getRankings());
        self::assertEquals(count($phaseData['rankings']), $phase->getRankings()->count());
        foreach ($phaseData['rankings'] as $rankingData) {
          self::assertTrue($phase->getRankings()->containsKey($rankingData['uniqueRank']));
          /** @var Ranking $ranking */
          $ranking = $phase->getRankings()->get($rankingData['uniqueRank']);

          $this->checkProperties($rankingData, $ranking, [
            'rank' => null,
            'uniqueRank' => null,
            'name' => ''
          ]);
          self::assertInstanceOf(Collection::class, $ranking->getTeams());
          self::assertEquals(count($rankingData['teamStartNumbers']), $ranking->getTeams()->count());
          foreach ($rankingData['teamStartNumbers'] as $startNumber) {
            self::assertTrue($competition->getTeams()->containsKey($startNumber));
            self::assertTrue($competition->getTeams()[$startNumber]->getStartNumber() == $startNumber);
          }
        }

        self::assertInstanceOf(Collection::class, $phase->getMatches());
        self::assertEquals(count($phaseData['matches']), $phase->getMatches()->count());
        foreach ($phaseData['matches'] as $matchData) {
          self::assertTrue($phase->getMatches()->containsKey($matchData['matchNumber']));
          /** @var Match $match */
          $match = $phase->getMatches()->get($matchData['matchNumber']);
          $this->checkProperties($matchData, $match, [
            'matchNumber' => null,
            'startTime' => ['transformer' => $dateTransformer, 'default' => null],
            'endTime' => ['transformer' => $dateTransformer, 'default' => null],
            'resultA' => null,
            'resultB' => null,
            'played' => null,
          ], array_merge($categories, ['result' => ['default' => null, 'enum' => Result::class]]));

          foreach (['A', 'B'] as $letter) {
            $method = 'getRankings' . $letter;
            /** @var Collection|Ranking[] $rankings */
            $rankings = $match->$method();
            $rankingsData = $matchData['rankings' . $letter . 'UniqueRanks'];
            self::assertInstanceOf(Collection::class, $rankings);
            self::assertEquals(count($rankingsData), $rankings->count());
            foreach ($rankingsData as $uniqueRank) {
              self::assertTrue($rankings->containsKey($uniqueRank));
              self::assertTrue($rankings[$uniqueRank]->getUniqueRank() == $uniqueRank);
            }
          }

          self::assertInstanceOf(Collection::class, $match->getGames());
          self::assertEquals(count($matchData['games']), $match->getGames()->count());
          foreach ($matchData['games'] as $gameData) {
            self::assertTrue($match->getGames()->containsKey($gameData['gameNumber']));
            /** @var Match $match */
            $game = $match->getGames()->get($gameData['gameNumber']);
            $this->checkProperties($gameData, $game, [
              'gameNumber' => null,
              'startTime' => ['transformer' => $dateTransformer, 'default' => null],
              'endTime' => ['transformer' => $dateTransformer, 'default' => null],
              'resultA' => null,
              'resultB' => null,
              'played' => null,
            ], array_merge($categories, ['result' => ['default' => null, 'enum' => Result::class]]));

            foreach (['A', 'B'] as $letter) {
              $method = 'getPlayers' . $letter;
              /** @var Collection|Player[] $players */
              $players = $game->$method();
              $playersData = $gameData['players' . $letter];
              self::assertInstanceOf(Collection::class, $players);
              self::assertEquals(count($playersData), $players->count());
              foreach ($playersData as $id) {
                self::assertTrue($players->containsKey($id));
                self::assertTrue($players[$id]->getPlayerId() == $id);
              }
            }
          }
        }
      }
    }
  }

  /** @noinspection PhpTooManyParametersInspection */
  /**
   * Creates a game
   * @param Player[] $playersA the players A
   * @param Player[] $playersB the players B
   * @param int $gameNumber the game number
   * @param int $resultA the result A
   * @param int $resultB the result B
   * @param int $result the result of the game
   * @param bool $played true if played and false if it didn't get played
   * @param \DateTime|null $startTime the start time
   * @param \DateTime|null $endTime the end time
   * @return Game
   */
  private function createGame(array $playersA, array $playersB, int $gameNumber, int $resultA = 1, int $resultB = 0,
                              int $result = Result::TEAM_A_WINS, bool $played = true, ?\DateTime $startTime = null,
                              ?\DateTime $endTime = null)
  {
    /** @var Game $game */
    $game = entity(Game::class)->create([
      'gameNumber' => $gameNumber,
      'resultA' => $resultA,
      'resultB' => $resultB,
      'result' => $result,
      'played' => $played,
      'startTime' => $startTime,
      'endTime' => $endTime]);
    foreach ($playersA as $player) {
      $game->getPlayersA()->set($player->getPlayerId(), $player);
    }

    foreach ($playersB as $player) {
      $game->getPlayersB()->set($player->getPlayerId(), $player);
    }

    return $game;
  }

  /** @noinspection PhpTooManyParametersInspection */
  /**
   * Creates a match
   * @param Ranking[] $rankingsA the rankings A
   * @param Ranking[] $rankingsB the rankings B
   * @param int $matchNumber the match number
   * @param int $resultA the result A
   * @param int $resultB the result B
   * @param int $result the result of the match
   * @param bool $played true if played and false if it didn't get played
   * @param \DateTime|null $startTime the start time
   * @param \DateTime|null $endTime the end time
   * @return Match
   */
  private function createMatch(array $rankingsA, array $rankingsB, int $matchNumber, int $resultA = 1, int $resultB = 0,
                               int $result = Result::TEAM_A_WINS, bool $played = true, ?\DateTime $startTime = null,
                               ?\DateTime $endTime = null)
  {
    /** @var Match $match */
    $match = entity(Match::class)->create([
      'matchNumber' => $matchNumber,
      'resultA' => $resultA,
      'resultB' => $resultB,
      'result' => $result,
      'played' => $played,
      'startTime' => $startTime,
      'endTime' => $endTime]);
    foreach ($rankingsA as $ranking) {
      $match->getRankingsA()->set($ranking->getUniqueRank(), $ranking);
    }

    foreach ($rankingsB as $ranking) {
      $match->getRankingsB()->set($ranking->getUniqueRank(), $ranking);
    }
    return $match;
  }

  /**
   * Creates a list of phases
   * @param int $number the number of phases
   * @param string[] $names a list of optional phase names
   * @param int[][] $nextPhases a dictionary which maps a index of a phase to all its direct successor indices
   * @return Phase[] the list of created phases
   */
  private function createPhases(int $number, array $names = [], array $nextPhases = []): array
  {
    /** @var Phase[] $phases */
    $phases = [];
    for ($i = 0; $i < $number; $i++) {
      $phase = entity(Phase::class)->create(['phaseNumber' => $i + 1]);
      if (count($names) > $i) {
        $phase->setName($names[$i]);
      }
      $phases[] = $phase;
    }
    foreach ($nextPhases as $key => $inds) {
      $phase = $phases[$key];
      foreach ($inds as $ind) {
        /** @var QualificationSystem $system */
        $system = entity(QualificationSystem::class)->create();
        $system->setPreviousPhase($phase);
        $system->setNextPhase($phases[$ind]);
      }
    }
    return $phases;
  }

  /**
   * Creates a ranking
   * @param Team[] $teams the teams of the ranking
   * @param int $rank the rank of the ranking
   * @param int|null $uniqueRank the unique rank of the ranking or null if it equals the rank
   * @return Ranking
   */
  private function createRanking(array $teams, int $rank, int $uniqueRank = null): Ranking
  {
    /** @var Ranking $ranking */
    $ranking = entity(Ranking::class)->create(['rank' => $rank,
      'uniqueRank' => $uniqueRank === null ? $rank : $uniqueRank]);
    foreach ($teams as $team) {
      $ranking->getTeams()->set($team->getStartNumber(), $team);
    }
    return $ranking;
  }
//</editor-fold desc="Private Methods">
}