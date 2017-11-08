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
                ],
                [
                  'name' => 'Group 2',
                  'groupNumber' => 2,
                  'gameMode' => 'CLASSIC',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'DYP',
                  'table' => 'LEONHART',
                ],
                [
                  'name' => 'Group 3',
                  'groupNumber' => 3,
                  'gameMode' => 'SPEEDBALL',
                  'organizingMode' => 'ELIMINATION',
                  'scoreMode' => 'BEST_OF_FIVE',
                  'teamMode' => 'SINGLE',
                  'table' => 'GARLANDO',
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
                ['groupNumber' => 1],
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
                ['groupNumber' => 1],
              ]
            ],
          ]
        ]
      ],
    ];

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
                ['groupNumber' => 1],
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
      $competition->setTournament($tournament);
    }

    /** @var Team[][] $teams */
    $teams = [];
    $teams[0] = $this->createTeams(4);
    $teams[1] = $this->createTeams(3, 3);
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

    for ($i = 0; $i < 4; $i++) {
      foreach ($teams[$i] as $team) {
        $team->setCompetition($competitions[$i]);
      }
      for ($j = 0; $j < count($phases[$i]); $j++) {
        $phase = $phases[$i][$j];
        $phase->setCompetition($competitions[$i]);
        for ($k = 0; $k < count($groups[$i][$j]); $k++) {
          $group = $groups[$i][$j][$k];
          $group->setPhase($phase);
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
                ['groupNumber' => 1, 'name' => 'G1'],
                ['groupNumber' => 2, 'name' => 'G2']
              ],
            ],
            [
              'phaseNumber' => 2,
              'name' => 'Ph2',
              'groups' => [
                ['groupNumber' => 1]
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
          ],
          'phases' => [
            [
              'phaseNumber' => 1,
              'name' => 'P1',
              'groups' => [
                ['groupNumber' => 1, 'name' => 'Gr1'],
                ['groupNumber' => 3, 'name' => 'G3'],
                ['groupNumber' => 4, 'name' => 'Gr4'],
              ],
            ],
            [
              'phaseNumber' => 2,
              'name' => 'Ph2',
              'groups' => [
                ['groupNumber' => 1],
                ['groupNumber' => 2, 'name' => 'G2'],
              ],
            ],
            [
              'phaseNumber' => 4,
              'name' => 'P4',
              'groups' => [
                ['groupNumber' => 1]
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
                ['groupNumber' => 1]
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
        }
      }
    }
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
//</editor-fold desc="Private Methods">
}