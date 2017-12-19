<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 8:48 PM
 */

namespace Tests\Integration;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
use App\Entity\Competition;
use App\Entity\Helpers\Result;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\QualificationSystem;
use App\Entity\Ranking;
use App\Entity\Team;
use App\Entity\Tournament;
use Doctrine\Common\Collections\Collection;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Tests\Helpers\AuthenticatedTestCase;

/**
 * Class TournamentTest
 * @package Tests\Integration
 */
class TournamentTest extends AuthenticatedTestCase
{
//<editor-fold desc="Public Methods">
  public function testCreateTournamentFull()
  {
    $players = $this->createPlayers(5);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'tournamentListId' => 'testList',
      'gameMode' => 'SPEEDBALL',
      'organizingMode' => 'ELIMINATION',
      'scoreMode' => 'BEST_OF_FIVE',
      'teamMode' => 'DOUBLE',
      'table' => 'ROBERTO_SPORT',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'gameMode' => 'CLASSIC',
          'organizingMode' => 'QUALIFICATION',
          'scoreMode' => 'ONE_SET',
          'teamMode' => 'SINGLE',
          'table' => 'MULTITABLE',
          'teams' => [
            ['name' => 'Team 1', 'rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['name' => 'Team 2', 'rank' => 1, 'startNumber' => 3, 'players' => [$players[1]->getId(),
              $players[2]->getId()]],
            ['name' => 'Team 3', 'rank' => 4, 'startNumber' => 2, 'players' =>
              [$players[2]->getId(), $players[3]->getId(), $players[4]->getId()]]
          ],
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
                  'endTime' => '2017-12-19 16:01:59 Europe/Vienna'
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
                ],
                [
                  'matchNumber' => 3,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [3],
                  'resultA' => 1,
                  'resultB' => 1,
                  'result' => 'DRAW',
                  'played' => true,
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
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'NULLED',
                  'played' => true
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
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 7,
                  'resultB' => 0,
                  'result' => 'TEAM_A_WINS',
                  'played' => false
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
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'DRAW',
                  'played' => false
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
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'NULLED',
                  'played' => false
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
                  'endTime' => '2017-12-19 16:00:00 Europe/Vienna'
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
            ['name' => 'Team 1', 'rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['name' => 'Team 2', 'rank' => 1, 'startNumber' => 3, 'players' => [$players[1]->getId()]],
            ['name' => 'Team 3', 'rank' => 4, 'startNumber' => 2, 'players' => [$players[2]->getId()]]
          ],
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
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1, 3],
                  'resultA' => 0,
                  'resultB' => 0,
                  'result' => 'NULLED',
                  'played' => false
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
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 1, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'rankings' => [
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 2]
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

  public function testDuplicatePlayerInTeam()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['name' => 'duplicate team', 'rank' => 1, 'startNumber' => 1,
              'players' => [$players[0]->getId(), $players[0]->getId()]],
            ['name' => 'other team', 'rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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
                ]
              ]
            ],
          ]
        ]
      ],
    ];

    /** @noinspection PhpUnhandledExceptionInspection */
    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception",
        "duplicateValue" => $players[0]->getId(), "arrayName" => "the player list of team duplicate team",
        "name" => "DuplicateException", "status" => 409]);

    /** @var \Doctrine\ORM\EntityRepository $repo */
    /** @noinspection PhpUndefinedMethodInspection */
    $repo = EntityManager::getRepository(Tournament::class);
    self::assertEquals(0, count($repo->findAll()));
  }

  public function testDuplicateStartNumber()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 1, 'players' => [$players[1]->getId()]],
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

  public function testDuplicatePhaseNumber()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testUnorderedTestNumberException()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testDuplicateNextPhaseNumber()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testNextPhaseNumberReferenceException()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testTeamStartNumberReferenceException()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testDuplicateRankingUniqueRank()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testDuplicateRankingTeamStartNumber()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testDuplicateMatchNumber()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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
                ],
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 5,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
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

  public function testDuplicateRankInMatchRankingsAAndB()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testDuplicateRankInMatchRankingsA()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testMatchRankingReferenceException()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'Test Tournament',
      'userIdentifier' => 'id0',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [$players[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$players[1]->getId()]],
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

  public function testTournamentUpdate()
  {
    /** @var Tournament $tournament */
    $tournament = entity(Tournament::class)->create([
      'userIdentifier' => 't1',
      'creator' => $this->user,
      'gameMode' => GameMode::CLASSIC,
    ]);
    /** @var Competition[] $competitions */
    $competitions = [
      entity(Competition::class)->create(['name' => 'Test Competition']),
      entity(Competition::class)->create(['name' => 'Test Competition 2']),
      entity(Competition::class)->create(['name' => 'Test Competition 4']),
      entity(Competition::class)->create(['name' => 'Test Competition 5'])];
    foreach ($competitions as $competition) {
      /** @noinspection PhpUnhandledExceptionInspection */
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


    for ($i = 0; $i < count($phases); $i++) {
      foreach ($teams[$i] as $team) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $team->setCompetition($competitions[$i]);
      }
      for ($j = 0; $j < count($phases[$i]); $j++) {
        $phase = $phases[$i][$j];
        /** @noinspection PhpUnhandledExceptionInspection */
        $phase->setCompetition($competitions[$i]);
        for ($k = 0; $k < count($rankings[$i][$j]); $k++) {
          /** @noinspection PhpUnhandledExceptionInspection */
          $rankings[$i][$j][$k]->setPhase($phase);
        }
        for ($k = 0; $k < count($matches[$i][$j]); $k++) {
          /** @noinspection PhpUnhandledExceptionInspection */
          $matches[$i][$j][$k]->setPhase($phase);
        }
      }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
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

    /** @noinspection PhpUnhandledExceptionInspection */
    $request = [
      'name' => 'New Name',
      'userIdentifier' => 't1',
      'gameMode' => 'OFFICIAL',
      'table' => 'GARLANDO',
      'competitions' => [
        [
          'name' => 'Test Competition',
          'teams' => [
            ['rank' => 4, 'startNumber' => 1, 'players' => [$teams[0][0]->getPlayers()->first()->getId()]],
            ['rank' => 3, 'startNumber' => 2, 'players' => [$teams[0][1]->getPlayers()->first()->getId()]],
            ['rank' => 2, 'startNumber' => 3, 'players' => [$teams[0][2]->getPlayers()->first()->getId()]],
            ['rank' => 1, 'startNumber' => 4, 'players' => [$teams[0][3]->getPlayers()->first()->getId()]],
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'name' => 'Ph1',
              'rankings' => [
                ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [1]],
                ['rank' => 1, 'uniqueRank' => 2, 'teamStartNumbers' => [2]]
              ],
              'matches' => [
                [
                  'matchNumber' => 1,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 2,
                  'resultB' => 7,
                  'result' => 'TEAM_B_WINS',
                  'played' => false,
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
                ],
                [
                  'matchNumber' => 2,
                  'rankingsAUniqueRanks' => [2],
                  'rankingsBUniqueRanks' => [1],
                  'resultA' => 5,
                  'resultB' => 5,
                  'result' => 'DRAW',
                  'played' => true,
                ]
              ]
            ],
          ]
        ],
        [
          'name' => 'Test Competition 2',
          'teams' => [
            ['rank' => 1, 'startNumber' => 1, 'players' => [
              $teams[1][0]->getPlayers()[0]->getId(),
              $teams[1][0]->getPlayers()[2]->getId(),
              $teams[1][1]->getPlayers()[0]->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [
              $teams[1][1]->getPlayers()[1]->getId(),
              $teams[1][1]->getPlayers()[2]->getId()]
            ],
            ['rank' => 2, 'startNumber' => 3, 'players' => [
              $teams[1][2]->getPlayers()[0]->getId()]
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
                ]
              ],
            ],
          ]
        ],
        [
          'name' => 'Test Competition 3',
          'teams' => [
            ['rank' => 3, 'startNumber' => 1, 'players' => [$teams[2][0]->getPlayers()->first()->getId()]],
            ['rank' => 2, 'startNumber' => 2, 'players' => [$teams[2][1]->getPlayers()->first()->getId()]],
            ['rank' => 1, 'startNumber' => 3, 'players' => [$teams[2][2]->getPlayers()->first()->getId()]],
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
    $new_tournament = $tournaments[0];
    self::assertEquals($tournament, $new_tournament);
    $this->assertTournamentInfoByRequest($request, $tournament);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * @param mixed[] $data
   * @param Tournament $tournament
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
    $this->checkProperties($data, $tournament, [
      'name' => null,
      'tournamentListId' => '',
    ], $categories);
    self::assertInstanceOf(Collection::class, $tournament->getCompetitions());
    self::assertEquals(count($data['competitions']), $tournament->getCompetitions()->count());
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertNotEmpty($tournament->getId());

    foreach ($data['competitions'] as $competition_data) {
      self::assertTrue($tournament->getCompetitions()->containsKey($competition_data['name']));
      /** @var Competition $competition */
      $competition = $tournament->getCompetitions()->get($competition_data['name']);
      $this->checkProperties($competition_data, $competition, [
        'name' => null,
      ], $categories);
      self::assertInstanceOf(Collection::class, $competition->getTeams());
      self::assertEquals(count($competition_data['teams']), $competition->getTeams()->count());
      foreach ($competition_data['teams'] as $team_data) {
        self::assertTrue($competition->getTeams()->containsKey($team_data['startNumber']));
        /** @var Team $team */
        $team = $competition->getTeams()->get($team_data['startNumber']);
        $this->checkProperties($team_data, $team, [
          'name' => null,
          'rank' => null,
          'startNumber' => null
        ]);
        self::assertInstanceOf(Collection::class, $team->getPlayers());
        self::assertEquals(count($team_data['players']), $team->getPlayers()->count());
        foreach ($team_data['players'] as $id) {
          $exists = false;
          foreach ($team->getPlayers() as $player) {
            /** @noinspection PhpUnhandledExceptionInspection */
            if ($player->getId() == $id) {
              $exists = true;
              break;
            }
          }
          self::assertTrue($exists);
        }
      }

      foreach ($competition_data['phases'] as $phase_data) {
        self::assertTrue($competition->getPhases()->containsKey($phase_data['phaseNumber']));
        /** @var Phase $phase */
        $phase = $competition->getPhases()->get($phase_data['phaseNumber']);

        $this->checkProperties($phase_data, $phase, [
          'name' => '',
          'phaseNumber' => null
        ], $categories);

        self::assertInstanceOf(Collection::class, $phase->getNextQualificationSystems());
        if (array_key_exists('nextPhaseNumbers', $phase_data)) {
          self::assertEquals(count($phase_data['nextPhaseNumbers']), $phase->getNextQualificationSystems()->count());
          foreach ($phase_data['nextPhaseNumbers'] as $phase_number) {
            $found = false;
            foreach ($phase->getNextQualificationSystems() as $system) {
              /** @noinspection PhpUnhandledExceptionInspection */
              if ($system->getNextPhase()->getPhaseNumber() === $phase_number) {
                $found = true;
                break;
              }
            }
            self::assertTrue($found);
          }
        } else {
          self::assertEquals(0, $phase->getNextQualificationSystems()->count());
        }

        self::assertInstanceOf(Collection::class, $phase->getRankings());
        self::assertEquals(count($phase_data['rankings']), $phase->getRankings()->count());
        foreach ($phase_data['rankings'] as $ranking_data) {
          self::assertTrue($phase->getRankings()->containsKey($ranking_data['uniqueRank']));
          /** @var Ranking $ranking */
          $ranking = $phase->getRankings()->get($ranking_data['uniqueRank']);

          $this->checkProperties($ranking_data, $ranking, [
            'rank' => null,
            'uniqueRank' => null,
            'name' => ''
          ]);
          self::assertInstanceOf(Collection::class, $ranking->getTeams());
          self::assertEquals(count($ranking_data['teamStartNumbers']), $ranking->getTeams()->count());
          foreach ($ranking_data['teamStartNumbers'] as $start_number) {
            self::assertTrue($competition->getTeams()->containsKey($start_number));
            /** @noinspection PhpUnhandledExceptionInspection */
            self::assertTrue($competition->getTeams()[$start_number]->getStartNumber() == $start_number);
          }
        }

        self::assertInstanceOf(Collection::class, $phase->getMatches());
        self::assertEquals(count($phase_data['matches']), $phase->getMatches()->count());
        foreach ($phase_data['matches'] as $match_data) {
          self::assertTrue($phase->getMatches()->containsKey($match_data['matchNumber']));
          /** @var Match $match */
          $match = $phase->getMatches()->get($match_data['matchNumber']);


          $datetimetz_transformer = function ($x) {
            return \DateTime::createFromFormat('Y-m-d H:i:s e', $x);
          };
          $this->checkProperties($match_data, $match, [
            'matchNumber' => null,
            'startTime' => ['transformer' => $datetimetz_transformer, 'default' => null],
            'endTime' => ['transformer' => $datetimetz_transformer, 'default' => null],
            'resultA' => null,
            'resultB' => null,
            'played' => null,
          ], array_merge($categories, ['result' => ['default' => null, 'enum' => Result::class]]));

          foreach (['A', 'B'] as $letter) {
            $method = 'getRankings' . $letter;
            /** @var Collection|Ranking[] $rankings */
            $rankings = $match->$method();
            $rankings_data = $match_data['rankings' . $letter . 'UniqueRanks'];
            self::assertInstanceOf(Collection::class, $rankings);
            self::assertEquals(count($rankings_data), $rankings->count());
            foreach ($rankings_data as $unique_rank) {
              self::assertTrue($rankings->containsKey($unique_rank));
              /** @noinspection PhpUnhandledExceptionInspection */
              self::assertTrue($rankings[$unique_rank]->getUniqueRank() == $unique_rank);
            }
          }
        }
      }
    }
  }

  /** @noinspection PhpDocMissingThrowsInspection */
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
      /** @noinspection PhpUnhandledExceptionInspection */
      $ranking->getTeams()->set($team->getStartNumber(), $team);
    }
    return $ranking;
  }

  /** @noinspection PhpDocMissingThrowsInspection */
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
      /** @noinspection PhpUnhandledExceptionInspection */
      $match->getRankingsA()->set($ranking->getUniqueRank(), $ranking);
    }

    foreach ($rankingsB as $ranking) {
      /** @noinspection PhpUnhandledExceptionInspection */
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
//</editor-fold desc="Private Methods">
}