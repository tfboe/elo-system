<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
use App\Entity\Competition;
use App\Entity\Group;
use App\Entity\Phase;
use App\Entity\Player;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Exceptions\DuplicateException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TournamentController
 * @package App\Http\Controllers
 */
class TournamentController extends BaseController
{
//<editor-fold desc="Fields">
  /**
   * @var string[]
   */
  private $tournamentSpecification;

  /**
   * @var string[]
   */
  private $competitionSpecification;

  /**
   * @var string[]
   */
  private $teamSpecification;

  /**
   * @var string[]
   */
  private $phaseSpecification;

  /**
   * @var string[]
   */
  private $groupSpecification;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">

  /**
   * creates or replaces an existing tournament
   *
   * @param Request $request the http request
   * @return JsonResponse
   */
  public function createOrReplaceTournament(Request $request): JsonResponse
  {
    $this->tournamentSpecification = [
      'userIdentifier' => ['validation' => 'required|string'],
      'name' => ['validation' => 'required|string'],
      'tournamentListId' => ['validation' => 'string', 'default' => ''],
      'competitions' => ['validation' => 'required|array|min:1', 'ignore' => True]
    ];
    $this->tournamentSpecification = array_merge($this->tournamentSpecification, $this->categoriesSpecifications(''));

    $this->competitionSpecification = [
      'competitions.*.name' => ['validation' => 'required|string|distinct'],
      'competitions.*.teams' => ['validation' => 'required|array|min:2', 'ignore' => True],
      'competitions.*.phases' => ['validation' => 'required|array|min:1', 'ignore' => True],
    ];
    $this->competitionSpecification = array_merge($this->competitionSpecification,
      $this->categoriesSpecifications('competitions.*.'));

    $this->teamSpecification = [
      'competitions.*.teams.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.teams.*.rank' => ['validation' => 'required|integer'],
      'competitions.*.teams.*.startNumber' => ['validation' => 'required|integer'],
      'competitions.*.teams.*.players' => ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.teams.*.players.*' => ['validation' => 'exists:App\Entity\Player,id', 'ignore' => True],
    ];

    $this->phaseSpecification = [
      'competitions.*.phases.*.phaseNumber' => ['validation' => 'required|integer'],
      'competitions.*.phases.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.phases.*.groups' => ['validation' => 'required|array|min:1', 'ignore' => True],
    ];
    $this->phaseSpecification = array_merge($this->phaseSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.'));

    $this->groupSpecification = [
      'competitions.*.phases.*.groups.*.groupNumber' => ['validation' => 'required|integer'],
      'competitions.*.phases.*.groups.*.name' => ['validation' => 'string', 'default' => ''],
    ];
    $this->groupSpecification = array_merge($this->groupSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.groups.*.'));

    $this->validateBySpecification($request, array_merge(
      $this->tournamentSpecification,
      $this->competitionSpecification,
      $this->teamSpecification,
      $this->phaseSpecification,
      $this->groupSpecification));

    return response()->json(['type' => $this->doCreateOrReplaceTournament($request)]);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * Returns a query input specification for the categories which are used in multiple entities (see CategoryTraits)
   * @param string $prefix the prefix for the keys, used if the category parameters are deeper in the input structure
   * @return string[] the query specification
   */
  private function categoriesSpecifications(string $prefix): array
  {
    return [
      $prefix . 'gameMode' => ['validation' => 'string|in:' . implode(",", GameMode::getNames()),
        'transformer' => $this->enumTransformer(GameMode::class), 'default' => null],
      $prefix . 'organizingMode' => ['validation' => 'string|in:' . implode(",", OrganizingMode::getNames()),
        'transformer' => $this->enumTransformer(OrganizingMode::class), 'default' => null],
      $prefix . 'scoreMode' => ['validation' => 'string|in:' . implode(",", ScoreMode::getNames()),
        'transformer' => $this->enumTransformer(ScoreMode::class), 'default' => null],
      $prefix . 'table' => ['validation' => 'string|in:' . implode(",", Table::getNames()),
        'transformer' => $this->enumTransformer(Table::class), 'default' => null],
      $prefix . 'teamMode' => ['validation' => 'string|in:' . implode(",", TeamMode::getNames()),
        'transformer' => $this->enumTransformer(TeamMode::class), 'default' => null],
    ];
  }

  /**
   * creates or replaces the tournament as specified in the request
   * @param Request $request the http request
   * @return string the type (either replace or create) of the operation
   */
  private function doCreateOrReplaceTournament(Request $request): string
  {
    assert(\Auth::user()->getId() != null);
    /** @var Tournament|null $tournament */
    $tournament = $this->em->getRepository(Tournament::class)->findOneBy(
      ['userIdentifier' => $request->input('userIdentifier'), 'creator' => \Auth::user()]);
    $type = 'replace';
    if ($tournament == null) {
      $tournament = new Tournament();
      $tournament->setCreator(\Auth::user());
      $this->em->persist($tournament);
      $type = 'create';
    }
    $this->setFromSpecification($tournament, $this->tournamentSpecification, $request->input());
    $this->replaceCompetitions($request, $tournament);
    $this->em->flush();

    return $type;
  }

  /**
   * Removes the given competition from the database
   * @param Competition $competition
   */
  private function removeCompetition(Competition $competition)
  {
    foreach ($competition->getPhases() as $phase) {
      $this->removePhase($phase);
    }
    $competition->getPhases()->clear();
    foreach ($competition->getTeams() as $team) {
      $this->removeTeam($team);
    }
    $competition->getTeams()->clear();
  }

  /**
   * Removes the given group from the database
   * @param Group $group
   */
  private function removeGroup(Group $group)
  {
    $this->em->remove($group);
  }

  /**
   * Removes the given phase from the database
   * @param Phase $phase
   */
  private function removePhase(Phase $phase)
  {
    foreach ($phase->getGroups() as $group) {
      $this->removeGroup($group);
    }
    $phase->getGroups()->clear();
    $this->em->remove($phase);
  }

  /**
   * Removes the given team from the database
   * @param Team $team
   */
  private function removeTeam(Team $team)
  {
    $this->em->remove($team);
  }

  /**
   * Replaces the competitions of the given tournament according to the request
   * @param Request $request the http request
   * @param Tournament $tournament the tournament to modify
   */
  private function replaceCompetitions(Request $request, Tournament $tournament)
  {
    $competition_names = [];
    foreach ($tournament->getCompetitions() as $competition) {
      $competition_names[$competition->getName()] = false;
    }
    foreach ($request->input('competitions') as $competition_values) {
      $competition = null;
      if (array_key_exists($competition_values['name'], $competition_names)) {
        $competition = $tournament->getCompetitions()->get($competition_values['name']);
        $this->setFromSpecification($competition, $this->competitionSpecification, $competition_values);
      } else {
        $competition = new Competition();
        $this->setFromSpecification($competition, $this->competitionSpecification, $competition_values);
        $competition->setTournament($tournament);
        $this->em->persist($competition);
      }
      $competition_names[$competition_values['name']] = true;
      $this->replaceTeams($competition, $competition_values['teams']);
      $this->replacePhases($competition, $competition_values['phases']);
    }
    foreach ($competition_names as $key => $used) {
      if (!$used) {
        $competition = $tournament->getCompetitions()->get($key);
        $tournament->getCompetitions()->remove($key);
        $this->removeCompetition($competition);
      }
    }
  }

  /**
   * Replaces the groups of the given phase according to the request
   * @param Phase $phase the phase to modify
   * @param mixed[] $values the request values for the groups
   */
  private function replaceGroups(Phase $phase, array $values)
  {
    $group_numbers = [];
    foreach ($phase->getGroups() as $group) {
      $group_numbers[$group->getGroupNumber()] = false;
    }
    foreach ($values as $group_values) {
      $group = null;
      if (array_key_exists($group_values['groupNumber'], $group_numbers)) {
        $group = $phase->getGroups()->get($group_values['groupNumber']);
        $this->setFromSpecification($group, $this->groupSpecification, $group_values);
      } else {
        $group = new Group();
        $this->setFromSpecification($group, $this->groupSpecification, $group_values);
        $group->setPhase($phase);
        $this->em->persist($group);
      }
      $group_numbers[$group_values['groupNumber']] = true;
    }
    foreach ($group_numbers as $key => $used) {
      if (!$used) {
        $group = $phase->getGroups()->get($key);
        $phase->getGroups()->remove($key);
        $this->removeGroup($group);
      }
    }
  }

  /**
   * Replaces the phases of the given competition according to the request
   * @param Competition $competition the competition to modify
   * @param mixed[] $values the request values for the phases
   */
  private function replacePhases(Competition $competition, array $values)
  {
    $phase_numbers = [];
    foreach ($competition->getPhases() as $phase) {
      $phase_numbers[$phase->getPhaseNumber()] = false;
    }
    foreach ($values as $phase_values) {
      $phase = null;
      if (array_key_exists($phase_values['phaseNumber'], $phase_numbers)) {
        $phase = $competition->getPhases()->get($phase_values['phaseNumber']);
        $this->setFromSpecification($phase, $this->phaseSpecification, $phase_values);
      } else {
        $phase = new Phase();
        $this->setFromSpecification($phase, $this->phaseSpecification, $phase_values);
        $phase->setCompetition($competition);
        $this->em->persist($phase);
      }
      $phase_numbers[$phase_values['phaseNumber']] = true;
      $this->replaceGroups($phase, $phase_values['groups']);
    }
    foreach ($phase_numbers as $key => $used) {
      if (!$used) {
        $phase = $competition->getPhases()->get($key);
        $competition->getPhases()->remove($key);
        $this->removePhase($phase);
      }
    }
  }

  /**
   * Replaces the players of the given team according to the request
   * @param Team $team the team to modify
   * @param mixed[] $team_values the request values for the team
   * @throws DuplicateException a player is specified twice for this team
   */
  private function replaceTeamPlayers(Team $team, array $team_values)
  {
    $player_ids = [];
    foreach ($team->getPlayers() as $player) {
      $player_ids[$player->getId()] = false;
    }
    foreach ($team_values['players'] as $player_id) {
      if (array_key_exists($player_id, $player_ids)) {
        if ($player_ids[$player_id] == true) {
          //duplicate player!
          throw new DuplicateException($player_id, 'player id',
            'the player list of team ' . $team->getName());
        }
      } else {
        $team->getPlayers()->add($this->em->find(Player::class, $player_id));
      }
      $player_ids[$player_id] = true;
    }
    foreach ($player_ids as $id => $used) {
      if (!$used) {
        $team->getPlayers()->removeElement($this->em->find(Player::class, $id));
      }
    }
  }

  /**
   * Replaces the teams of the given competition according to the request
   * @param Competition $competition the competition to modify
   * @param mixed[] $values the request values for the teams
   * @throws DuplicateException a team start number is occurring twice for this team
   */
  private function replaceTeams(Competition $competition, array $values)
  {
    $old_start_numbers = [];
    foreach ($competition->getTeams() as $team) {
      $old_start_numbers[$team->getStartNumber()] = false;
    }
    foreach ($values as $team_values) {
      if (array_key_exists($team_values['startNumber'], $old_start_numbers)) {
        if ($old_start_numbers[$team_values['startNumber']] == true) {
          //duplicate start number!
          throw new DuplicateException($team_values['startNumber'], 'start number',
            'the team list of competition ' . $competition->getName());
        }
        $team = $competition->getTeams()->get($team_values['startNumber']);
        $this->setFromSpecification($team, $this->teamSpecification, $team_values);
      } else {
        $team = new Team();
        $this->setFromSpecification($team, $this->teamSpecification, $team_values);
        $team->setCompetition($competition);
        $this->em->persist($team);
      }
      $old_start_numbers[$team_values['startNumber']] = true;
      $this->replaceTeamPlayers($team, $team_values);
    }
    foreach ($old_start_numbers as $key => $used) {
      if (!$used) {
        $team = $competition->getTeams()->get($key);
        $competition->getTeams()->remove($key);
        $this->removeTeam($team);
      }
    }
  }
//</editor-fold desc="Private Methods">
}
