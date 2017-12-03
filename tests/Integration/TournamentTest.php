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
use App\Entity\Group;
use App\Entity\Phase;
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
              'groups' => [
                [
                  'name' => 'Group 1',
                  'groupNumber' => 1,
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DOUBLE',
                  'table' => 'MULTITABLE',
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 2, 'teamStartNumbers' => [3], 'uniqueRank' => 2],
                    ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 3],
                  ]
                ],
                [
                  'name' => 'Group 2',
                  'groupNumber' => 2,
                  'gameMode' => 'CLASSIC',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DYP',
                  'table' => 'LEONHART',
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 2, 'teamStartNumbers' => [3], 'uniqueRank' => 2]
                  ]
                ],
                [
                  'name' => 'Group 3',
                  'groupNumber' => 3,
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'SINGLE',
                  'table' => 'GARLANDO',
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [3], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 3],
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
                  ]
                ],
              ]
            ],
            [
              'name' => 'Phase 2',
              'phaseNumber' => 2,
              'gameMode' => 'SPEEDBALL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_FIVE',
              'teamMode' => 'DOUBLE',
              'table' => 'GARLANDO',
              'groups' => [
                [
                  'name' => 'Group 1',
                  'groupNumber' => 1,
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'SINGLE',
                  'table' => 'GARLANDO',
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [3], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                  ]
                ]
              ]
            ],
            [
              'name' => 'Phase 3',
              'phaseNumber' => 3,
              'gameMode' => 'OFFICIAL',
              'organizingMode' => 'ELIMINATION',
              'scoreMode' => 'BEST_OF_THREE',
              'teamMode' => 'DYP',
              'table' => 'LEONHART',
              'groups' => [
                [
                  'name' => 'Group 1',
                  'groupNumber' => 1,
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'SINGLE',
                  'table' => 'GARLANDO',
                  'rankings' => [
                    ['rank' => 3, 'teamStartNumbers' => [3], 'uniqueRank' => 1], //is this allowed???
                    ['rank' => 2, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 3],
                  ]
                ]
              ]
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
              'groups' => [
                [
                  'name' => 'Group 1',
                  'groupNumber' => 1,
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'SINGLE',
                  'table' => 'GARLANDO',
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
                    ['rank' => 2, 'teamStartNumbers' => [3], 'uniqueRank' => 3],
                  ]
                ]
              ]
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 2]
                  ]
                ],
              ]
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

  public function testDuplicatePlayerInTeam()
  {
    $players = $this->createPlayers(2);
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                  ]
                ],
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                  ]
                ],
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                  ]
                ]
              ]
            ],
            [
              'phaseNumber' => 1,
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                    ['rank' => 2, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
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

  public function testDuplicateGroupNumber()
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                  ]
                ],
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                    ['rank' => 2, 'teamStartNumbers' => [1], 'uniqueRank' => 2],
                  ]
                ],
              ]
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the group list of the phase 1 of the competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 1],
                  ]
                ],
              ]
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the ranking list of group (Nr 1) of the phase 1 of the competition Test Competition",
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 1, 'teamStartNumbers' => [1, 1], 'uniqueRank' => 1],
                    ['rank' => 1, 'teamStartNumbers' => [2], 'uniqueRank' => 2],
                  ]
                ],
              ]
            ],
          ]
        ],
      ],
    ];

    $this->jsonAuth('POST', '/createOrReplaceTournament', $request)
      ->seeStatusCode(409)->seeJsonEquals(["message" => "Duplicate Exception", "duplicateValue" => 1,
        "arrayName" => "the team list of ranking (Unique rank 1) of the group (Nr 1) of the phase 1 of the " .
          "competition Test Competition",
        "name" => "DuplicateException", "status" => 409]);

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
    $phases[1] = $this->createPhases(3, ['P1', 'P2', 'P3']);
    $phases[2] = $this->createPhases(1);
    $phases[3] = $this->createPhases(1);

    /** @var Group[][][] $groups */
    $groups = [];
    $groups[0] = [$this->createGroups(1, ['Main Group'])];
    $groups[1] = [
      $this->createGroups(2, ['G1', 'G2']),
      $this->createGroups(1, ['G']),
      $this->createGroups(3, ['G1', 'G2', 'G3'])
    ];
    $groups[2] = [$this->createGroups(1)];
    $groups[3] = [$this->createGroups(1)];

    /** @var Ranking[][][][] $rankings */
    $rankings = [];
    $rankings[0] = [[[
      $this->createRanking([$teams[0][0]], 1),
      $this->createRanking([$teams[0][1]], 2),
      $this->createRanking([$teams[0][2]], 3),
      $this->createRanking([$teams[0][3]], 3, 4)
    ]]];
    $rankings[1] = [
      [
        [
          $this->createRanking([$teams[1][0]], 1),
          $this->createRanking([$teams[1][1]], 2),
        ],
        [
          $this->createRanking([$teams[1][2]], 1),
          $this->createRanking([$teams[1][3]], 1, 2),
        ]
      ],
      [
        [
          $this->createRanking([$teams[1][0]], 1),
          $this->createRanking([$teams[1][1]], 2),
          $this->createRanking([$teams[1][2]], 3),
          $this->createRanking([$teams[1][3]], 4)
        ]
      ],
      [
        [
          $this->createRanking([$teams[1][0]], 1),
          $this->createRanking([$teams[1][1]], 2),
        ],
        [
          $this->createRanking([$teams[1][2]], 1),
          $this->createRanking([$teams[1][3]], 1, 2),
        ],
        [
          $this->createRanking([$teams[1][0]], 1),
          $this->createRanking([$teams[1][2]], 1, 2),
        ]
      ],
    ];
    $rankings[2] = [[[
      $this->createRanking([$teams[2][0]], 1),
      $this->createRanking([$teams[2][1]], 2),
    ]]];
    $rankings[3] = [[[
      $this->createRanking([$teams[3][0]], 1),
      $this->createRanking([$teams[3][1]], 2),
      $this->createRanking([$teams[3][2]], 2, 3),
    ]]];


    for ($i = 0; $i < 4; $i++) {
      foreach ($teams[$i] as $team) {
        /** @noinspection PhpUnhandledExceptionInspection */
        $team->setCompetition($competitions[$i]);
      }
      for ($j = 0; $j < count($phases[$i]); $j++) {
        $phase = $phases[$i][$j];
        /** @noinspection PhpUnhandledExceptionInspection */
        $phase->setCompetition($competitions[$i]);
        for ($k = 0; $k < count($groups[$i][$j]); $k++) {
          $group = $groups[$i][$j][$k];
          /** @noinspection PhpUnhandledExceptionInspection */
          $group->setPhase($phase);
          for ($l = 0; $l < count($rankings[$i][$j][$k]); $l++) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $rankings[$i][$j][$k][$l]->setGroup($group);
          }
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
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'name' => 'G1',
                  'rankings' => [
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 2, 'teamStartNumbers' => [2]]
                  ]
                ],
                [
                  'groupNumber' => 2,
                  'name' => 'G2',
                  'rankings' => [
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [4]],
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [3]]
                  ]
                ]
              ],
            ],
            [
              'phaseNumber' => 2,
              'name' => 'Ph2',
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [4]]
                  ]
                ]
              ],
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'name' => 'Gr1',
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]]
                  ]
                ],
                [
                  'groupNumber' => 3, 'name' => 'G3',
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [2]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]]
                  ]
                ],
                [
                  'groupNumber' => 4, 'name' => 'Gr4',
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [2]]
                  ]
                ],
              ],
            ],
            [
              'phaseNumber' => 2,
              'name' => 'Ph2',
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]]
                  ]
                ],
                [
                  'groupNumber' => 2, 'name' => 'G2',
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]],
                    ['rank' => 2, 'uniqueRank' => 3, 'teamStartNumbers' => [2]]
                  ]
                ],
              ],
            ],
            [
              'phaseNumber' => 4,
              'name' => 'P4',
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]],
                    ['rank' => 3, 'uniqueRank' => 3, 'teamStartNumbers' => [2]]
                  ]
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
              'groups' => [
                [
                  'groupNumber' => 1,
                  'rankings' => [
                    ['rank' => 2, 'uniqueRank' => 2, 'teamStartNumbers' => [1]],
                    ['rank' => 1, 'uniqueRank' => 1, 'teamStartNumbers' => [3]],
                    ['rank' => 3, 'uniqueRank' => 3, 'teamStartNumbers' => [2]]
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
        self::assertInstanceOf(Collection::class, $phase->getGroups());
        self::assertEquals(count($phase_data['groups']), $phase->getGroups()->count());

        foreach ($phase_data['groups'] as $group_data) {
          self::assertTrue($phase->getGroups()->containsKey($group_data['groupNumber']));
          /** @var Group $group */
          $group = $phase->getGroups()->get($group_data['groupNumber']);

          $this->checkProperties($group_data, $group, [
            'name' => '',
            'groupNumber' => null
          ], $categories);
          self::assertInstanceOf(Collection::class, $group->getRankings());
          self::assertEquals(count($group_data['rankings']), $group->getRankings()->count());

          foreach ($group_data['rankings'] as $ranking_data) {
            self::assertTrue($group->getRankings()->containsKey($ranking_data['uniqueRank']));
            /** @var Ranking $ranking */
            $ranking = $group->getRankings()->get($ranking_data['uniqueRank']);

            $this->checkProperties($ranking_data, $ranking, [
              'rank' => null,
              'uniqueRank' => null,
              'name' => ''
            ]);
            self::assertInstanceOf(Collection::class, $ranking->getTeams());
            self::assertEquals(count($ranking_data['teamStartNumbers']), $ranking->getTeams()->count());
            foreach ($ranking_data['teamStartNumbers'] as $start_number) {
              $exists = false;
              foreach ($competition->getTeams() as $team) {
                /** @noinspection PhpUnhandledExceptionInspection */
                if ($team->getStartNumber() == $start_number) {
                  $exists = true;
                  break;
                }
              }
              self::assertTrue($exists);
            }
          }
        }
      }
    }
  }

  /**
   * Creates a list of groups
   * @param int $number the number of groups
   * @param string[] $names a list of optional group names
   * @return Phase[] the list of created groups
   */
  private function createGroups(int $number, array $names = []): array
  {
    $groups = [];
    for ($i = 0; $i < $number; $i++) {
      $group = entity(Group::class)->create(['groupNumber' => $i + 1]);
      if (count($names) > $i) {
        $group->setName($names[$i]);
      }
      $groups[] = $group;
    }
    return $groups;
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

  /**
   * Creates a list of phases
   * @param int $number the number of phases
   * @param string[] $names a list of optional phase names
   * @return Phase[] the list of created phases
   */
  private function createPhases(int $number, array $names = []): array
  {
    $phases = [];
    for ($i = 0; $i < $number; $i++) {
      $phase = entity(Phase::class)->create(['phaseNumber' => $i + 1]);
      if (count($names) > $i) {
        $phase->setName($names[$i]);
      }
      $phases[] = $phase;
    }
    return $phases;
  }
//</editor-fold desc="Private Methods">
}