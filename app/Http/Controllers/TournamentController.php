<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Helpers\Result;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\Player;
use App\Entity\QualificationSystem;
use App\Entity\Ranking;
use App\Entity\RankingSystem;
use App\Entity\Team;
use App\Entity\Tournament;
use App\Exceptions\DuplicateException;
use App\Exceptions\ReferenceException;
use App\Exceptions\UnorderedPhaseNumberException;
use App\Helpers\Level;
use App\Service\RankingSystemServiceInterface;
use Doctrine\Common\Collections\Collection;
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
  private $rankingSpecification;

  /**
   * @var string[]
   */
  private $matchSpecification;

  /**
   * @var string[]
   */
  private $gameSpecification;

  /**
   * @var RankingSystem[][]
   */
  private $rankingSystems;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">

  /**
   * creates or replaces an existing tournament
   *
   * @param Request $request the http request
   * @param RankingSystemServiceInterface $rss ranking system service
   * @return JsonResponse
   * @throws DuplicateException two competitions have the same name or a team start number is occurring twice or
   *                            a player is specified twice for a team or two phases of a competition have the same
   *                            phase number or a unique rank is occurring twice or
   *                            a team start number is specified twice for a ranking or a match number is occurring
   *                            twice in a phase or a unique rank is specified twice for the two teams in a match in a
   *                            phase or a game number is occurring twice or a player id is specified twice for the
   *                            players of a game
   * @throws ReferenceException a referenced phase number in the next phases array does not exist or a referenced team
   *                            start number in the team values does not exist or a referenced unique rank in the
   *                            rankings lists of a match does not exist in the rankings of the corresponding phase or
   *                            a player of a team is not in the players lists of this team
   * @throws UnorderedPhaseNumberException
   */
  public function createOrReplaceTournament(Request $request, RankingSystemServiceInterface $rss): JsonResponse
  {
    $this->tournamentSpecification = [
      'userIdentifier' => ['validation' => 'required|string'],
      'name' => ['validation' => 'required|string'],
      'tournamentListId' => ['validation' => 'string', 'default' => ''],
      'competitions' => ['validation' => 'required|array|min:1', 'ignore' => True],
      'startTime' => ['validation' => 'date_format:' . $this->datetimetzFormat,
        'transformer' => $this->datetimetzTransformer(), 'default' => null],
      'endTime' => ['validation' => 'date_format:' . $this->datetimetzFormat,
        'transformer' => $this->datetimetzTransformer(), 'default' => null],
    ];
    $this->tournamentSpecification = array_merge(
      $this->tournamentSpecification, $this->categoriesSpecifications(''),
      $this->timeSpecifications(''));

    $this->competitionSpecification = [
      'competitions.*.name' => ['validation' => 'required|string|distinct'],
      'competitions.*.teams' => ['validation' => 'required|array|min:2', 'ignore' => True],
      'competitions.*.phases' => ['validation' => 'required|array|min:1', 'ignore' => True]
    ];
    $this->competitionSpecification = array_merge($this->competitionSpecification,
      $this->categoriesSpecifications('competitions.*.'),
      $this->timeSpecifications('competitions.*.'));

    $this->teamSpecification = [
      'competitions.*.teams.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.teams.*.rank' => ['validation' => 'required|integer'],
      'competitions.*.teams.*.startNumber' => ['validation' => 'required|integer|min:1'],
      'competitions.*.teams.*.players' => ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.teams.*.players.*' => ['validation' => 'exists:App\Entity\Player,id', 'ignore' => True],
    ];

    $this->phaseSpecification = [
      'competitions.*.phases.*.phaseNumber' => ['validation' => 'required|integer|min:1'],
      'competitions.*.phases.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.phases.*.nextPhaseNumbers' => ['validation' => 'array', 'ignore' => True],
      'competitions.*.phases.*.nextPhaseNumbers.*' => ['validation' => 'integer|min:1', 'ignore' => True],
      'competitions.*.phases.*.rankings' => ['validation' => 'required|array|min:2', 'ignore' => True],
      'competitions.*.phases.*.matches' => ['validation' => 'required|array|min:1', 'ignore' => True]
    ];
    $this->phaseSpecification = array_merge($this->phaseSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.'),
      $this->timeSpecifications('competitions.*.phases.*.'));

    $this->rankingSpecification = [
      'competitions.*.phases.*.rankings.*.uniqueRank' => ['validation' => 'required|integer|min:1'],
      'competitions.*.phases.*.rankings.*.teamStartNumbers' =>
        ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.phases.*.rankings.*.teamStartNumbers.*' =>
        ['validation' => 'required|integer|min:1', 'ignore' => True],
      'competitions.*.phases.*.rankings.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.phases.*.rankings.*.rank' => ['validation' => 'required|integer|min:1'],
    ];

    $this->matchSpecification = [
      'competitions.*.phases.*.matches.*.matchNumber' => ['validation' => 'required|integer|min:1'],
      'competitions.*.phases.*.matches.*.rankingsAUniqueRanks' =>
        ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.phases.*.matches.*.rankingsAUniqueRanks.*' =>
        ['validation' => 'required|integer|min:1', 'ignore' => True],
      'competitions.*.phases.*.matches.*.rankingsBUniqueRanks' =>
        ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.phases.*.matches.*.rankingsBUniqueRanks.*' =>
        ['validation' => 'required|integer|min:1', 'ignore' => True],
      'competitions.*.phases.*.matches.*.games' => ['validation' => 'required|array|min:1', 'ignore' => True],
    ];

    $this->matchSpecification = array_merge($this->matchSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.matches.*.'),
      $this->resultSpecifications('competitions.*.phases.*.matches.*.'),
      $this->timeSpecifications('competitions.*.phases.*.matches.*.'));

    $this->gameSpecification = [
      'competitions.*.phases.*.matches.*.games.*.gameNumber' => ['validation' => 'required|integer|min:1'],
      'competitions.*.phases.*.matches.*.games.*.playersA' =>
        ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.phases.*.matches.*.games.*.playersA.*' =>
        ['validation' => 'exists:App\Entity\Player,id', 'ignore' => True],
      'competitions.*.phases.*.matches.*.games.*.playersB' =>
        ['validation' => 'required|array|min:1', 'ignore' => True],
      'competitions.*.phases.*.matches.*.games.*.playersB.*' =>
        ['validation' => 'exists:App\Entity\Player,id', 'ignore' => True]
    ];

    $this->gameSpecification = array_merge($this->gameSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.matches.*.games.*.'),
      $this->resultSpecifications('competitions.*.phases.*.matches.*.games.*.'),
      $this->timeSpecifications('competitions.*.phases.*.matches.*.games.*.'));


    $this->validateBySpecification($request, array_merge(
      $this->tournamentSpecification,
      $this->competitionSpecification,
      $this->teamSpecification,
      $this->phaseSpecification,
      $this->rankingSpecification,
      $this->matchSpecification,
      $this->gameSpecification));

    return response()->json(['type' => $this->doCreateOrReplaceTournament($request, $rss)]);
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
   * Returns a query input specification for the result part of an object which is used multiple entities
   * (see ResultEntity)
   * @param string $prefix the prefix for the keys, used if the result parameters are deeper in the input structure
   * @return string[] the query specification
   */
  private function resultSpecifications(string $prefix): array
  {
    return [
      $prefix . 'resultA' => ['validation' => 'required|integer|min:0'],
      $prefix . 'resultB' => ['validation' => 'required|integer|min:0'],
      $prefix . 'result' => ['validation' => 'required|string|in:' . implode(",", Result::getNames()),
        'transformer' => $this->enumTransformer(Result::class)],
      $prefix . 'played' => ['validation' => 'required|boolean'],
    ];
  }

  /**
   * Returns a query input specification for the time part of an object which is used in multiple entities
   * (see TimeEntity)
   * @param string $prefix the prefix for the keys, used if the result parameters are deeper in the input structure
   * @return string[] the query specification
   */
  private function timeSpecifications(string $prefix): array
  {
    return [
      $prefix . 'startTime' => ['validation' => 'date_format:' . $this->datetimetzFormat,
        'transformer' => $this->datetimetzTransformer(), 'default' => null],
      $prefix . 'endTime' => ['validation' => 'date_format:' . $this->datetimetzFormat,
        'transformer' => $this->datetimetzTransformer(), 'default' => null]
    ];
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * creates or replaces the tournament as specified in the request
   * @param Request $request the http request
   * @param RankingSystemServiceInterface $rss ranking system service
   * @return string the type (either replace or create) of the operation
   * @throws DuplicateException two competitions have the same name or a team start number is occurring twice or
   *                            a player is specified twice for a team or two phases of a competition have the same
   *                            phase number or a unique rank is occurring twice or
   *                            a team start number is specified twice for a ranking or a match number is occurring
   *                            twice in a phase or a unique rank is specified twice for the two teams in a match in a
   *                            phase or a game number is occurring twice or a player id is specified twice for the
   *                            players of a game
   * @throws ReferenceException a referenced phase number in the next phases array does not exist or a referenced team
   *                            start number in the team values does not exist or a referenced unique rank in the
   *                            rankings lists of a match does not exist in the rankings of the corresponding phase  or
   *                            a player of a team is not in the players lists of this team
   * @throws UnorderedPhaseNumberException
   */
  private function doCreateOrReplaceTournament(Request $request, RankingSystemServiceInterface $rss): string
  {
    /** @noinspection PhpUnhandledExceptionInspection */
    assert(\Auth::user()->getId() != null);
    /** @var Tournament|null $tournament */
    $tournament = $this->em->getRepository(Tournament::class)->findOneBy(
      ['userIdentifier' => $request->input('userIdentifier'), 'creator' => \Auth::user()]);
    $type = 'replace';

    $earliest_influence_times = $tournament === null ? [] : $rss->getRankingSystemsEarliestInfluences($tournament);

    /** @var RankingSystem[] $ranking_systems */
    $ranking_systems = $this->em->getRepository(RankingSystem::class)->findAll();
    $this->rankingSystems = [];
    foreach (Level::getValues() as $value) {
      $this->rankingSystems[$value] = [];
    }
    foreach ($ranking_systems as $ranking_system) {
      if ($ranking_system->getDefaultForLevel() !== null) {
        $this->rankingSystems[$ranking_system->getDefaultForLevel()][] = $ranking_system;
      }
    }

    if ($tournament == null) {
      $tournament = new Tournament();
      $tournament->setCreator(\Auth::user());
      $this->em->persist($tournament);
      foreach ($this->rankingSystems[Level::TOURNAMENT] as $ranking_system) {
        /** @noinspection PhpUnhandledExceptionInspection
         * ranking_system comes from the database and therefore has an id */
        $tournament->getRankingSystems()->set($ranking_system->getId(), $ranking_system);
        /** @noinspection PhpUnhandledExceptionInspection
         * tournament is persisted and therefore has an id */
        $ranking_system->getTournaments()->set($tournament->getId(), $tournament);
      }
      $type = 'create';
    }

    $this->setFromSpecification($tournament, $this->tournamentSpecification, $request->input());
    /** @noinspection PhpUnhandledExceptionInspection */ //the tournament has a name since its required in the request
    $this->replaceCompetitions($request, $tournament);

    $rss->adaptOpenSyncFromValues($tournament, $earliest_influence_times);
    $this->em->flush();
    $rss->recalculateRankingSystems();
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

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * Removes the given phase from the database
   * @param Phase $phase
   */
  private function removePhase(Phase $phase)
  {
    foreach ($phase->getMatches() as $match) {
      $this->removeMatch($match);
    }
    $phase->getMatches()->clear();

    foreach ($phase->getPreviousQualificationSystems()->toArray() as $system) {
      /** @var $system QualificationSystem */
      $phase->getPreviousQualificationSystems()->removeElement($system);
      /** @noinspection PhpUnhandledExceptionInspection */
      // all systems in the array have a next phase and the phase has a phase number
      $system->getPreviousPhase()->getNextQualificationSystems()->removeElement($system);
      $this->em->remove($system);
    }

    foreach ($phase->getNextQualificationSystems()->toArray() as $system) {
      /** @var $system QualificationSystem */
      $phase->getNextQualificationSystems()->removeElement($system);
      /** @noinspection PhpUnhandledExceptionInspection */
      // all systems in the array have a next phase and the phase has a phase number
      $system->getNextPhase()->getPreviousQualificationSystems()->removeElement($system);
      $this->em->remove($system);
    }
    foreach ($phase->getRankings() as $ranking) {
      $this->removeRanking($ranking);
    }
    $phase->getRankings()->clear();
    $this->em->remove($phase);
  }

  /**
   * Removes the given ranking from the database
   * @param Ranking $ranking
   */
  private function removeRanking(Ranking $ranking)
  {
    $this->em->remove($ranking);
  }

  /**
   * Removes the given match from the database
   * @param Match $match
   */
  private function removeMatch(Match $match)
  {
    foreach ($match->getGames() as $game) {
      $this->removeGame($game);
    }
    $match->getGames()->clear();
    $this->em->remove($match);
  }

  /**
   * Removes the given game from the database
   * @param Game $game
   */
  private function removeGame(Game $game)
  {
    $this->em->remove($game);
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
   * @throws DuplicateException two competitions have the same name or a team start number is occurring twice or
   *                            a player is specified twice for a team or two phases of a competition have the same
   *                            phase number or a unique rank is occurring twice or
   *                            a team start number is specified twice for a ranking or a match number is occurring
   *                            twice in a phase or a unique rank is specified twice for the two teams in a match in a
   *                            phase or a game number is occurring twice or a player id is specified twice for the
   *                            players of a game
   * @throws \App\Exceptions\ValueNotSet the tournament has no name
   * @throws ReferenceException a referenced phase number in the next phases array does not exist or a referenced team
   *                            start number in the team values does not exist or a referenced unique rank in the
   *                            rankings lists of a match does not exist in the rankings of the corresponding phase  or
   *                            a player of a team is not in the players lists of this team
   * @throws UnorderedPhaseNumberException
   */
  private function replaceCompetitions(Request $request, Tournament $tournament)
  {
    $competition_names = [];
    foreach ($tournament->getCompetitions() as $competition) {
      /** @noinspection PhpUnhandledExceptionInspection */ //all competitions in the array have a name
      $competition_names[$competition->getName()] = false;
    }
    foreach ($request->input('competitions') as $competition_values) {
      $competition = null;
      if (array_key_exists($competition_values['name'], $competition_names)) {
        // competition names are ensured to be unique by the request validation
        assert($competition_names[$competition_values['name']] === false);
        $competition = $tournament->getCompetitions()->get($competition_values['name']);
        $this->setFromSpecification($competition, $this->competitionSpecification, $competition_values);
      } else {
        $competition = new Competition();
        $this->setFromSpecification($competition, $this->competitionSpecification, $competition_values);
        $competition->setTournament($tournament);
        $this->em->persist($competition);
        foreach ($this->rankingSystems[Level::COMPETITION] as $ranking_system) {
          $competition->getRankingSystems()->set($ranking_system->getId(), $ranking_system);
          $ranking_system->getCompetitions()->set($competition->getId(), $competition);
        }
      }
      $competition_names[$competition_values['name']] = true;
      /** @noinspection PhpUnhandledExceptionInspection */ // the competition has a name since it is required int the
      // request
      $this->replaceTeams($competition, $competition_values['teams']);
      /** @noinspection PhpUnhandledExceptionInspection */ // the competition has a name since it is required int the
      // request
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

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * Replaces the phases of the given competition according to the request
   * @param Competition $competition the competition to modify
   * @param mixed[] $values the request values for the phases
   * @throws DuplicateException two phases of a competition have the same phase number or a unique rank is occurring
   *                            twice or a team start number is specified twice for a ranking or a match number is
   *                            occurring twice or a unique rank is specified twice for the two teams in a match or a
   *                            game number is occurring twice or a player id is specified twice for the players of a
   *                            game
   * @throws \App\Exceptions\ValueNotSet the competition has no name
   * @throws ReferenceException a referenced phase number in the next phases array does not exist or a referenced team
   *                            start number in the team values does not exist or a referenced unique rank in the
   *                            rankings lists of a match does not exist in the rankings of the phase or a player of a
   *                            team is not in the players lists of this team
   * @throws UnorderedPhaseNumberException
   */
  private function replacePhases(Competition $competition, array $values)
  {
    $phase_numbers = [];
    foreach ($competition->getPhases() as $phase) {
      /** @noinspection PhpUnhandledExceptionInspection */ //all phases in the array have a phase number
      $phase_numbers[$phase->getPhaseNumber()] = false;
    }
    foreach ($values as $phase_values) {
      $phase = null;
      if (array_key_exists($phase_values['phaseNumber'], $phase_numbers)) {
        if ($phase_numbers[$phase_values['phaseNumber']] == true) {
          //duplicate phase number
          throw new DuplicateException($phase_values['phaseNumber'], 'phase number',
            'the phase list of the competition ' . $competition->getName());
        }
        $phase = $competition->getPhases()->get($phase_values['phaseNumber']);
        $this->setFromSpecification($phase, $this->phaseSpecification, $phase_values);
      } else {
        $phase = new Phase();
        $this->setFromSpecification($phase, $this->phaseSpecification, $phase_values);
        /** @noinspection PhpUnhandledExceptionInspection */ // the phase has a phase number since it is required in the
        // request
        $phase->setCompetition($competition);
        $this->em->persist($phase);
        foreach ($this->rankingSystems[Level::PHASE] as $ranking_system) {
          $phase->getRankingSystems()->set($ranking_system->getId(), $ranking_system);
          $ranking_system->getPhases()->set($phase->getId(), $phase);
        }
      }
      $phase_numbers[$phase_values['phaseNumber']] = true;
      $this->replaceRankings($phase, $phase_values['rankings']);
      $this->replaceMatches($phase, $phase_values['matches']);
    }

    foreach ($phase_numbers as $key => $used) {
      if (!$used) {
        $phase = $competition->getPhases()[$key];
        $competition->getPhases()->remove($key);
        $this->removePhase($phase);
      }
    }
    foreach ($values as $phase_values) {
      //replace next phases
      $phase = $competition->getPhases()->get($phase_values['phaseNumber']);
      $this->replacePhaseNextPhases($phase, array_key_exists('nextPhaseNumbers', $phase_values) ?
        $phase_values['nextPhaseNumbers'] : []);
    }
  }

  /**
   * Replaces the next phases of the given phase according to the request.
   * It also replaces the corresponding previous phases of the next phases.
   * @param Phase $phase the phase to modify
   * @param int[] $nextPhaseNumbers list of next phase numbers
   * @throws DuplicateException a phase number was specified twice
   * @throws \App\Exceptions\ValueNotSet the given phase has no phase number or no competition or its competition has
   *                                     no name
   * @throws ReferenceException a referenced phase number in the next phases array does not exist
   * @throws UnorderedPhaseNumberException
   */
  private function replacePhaseNextPhases(Phase $phase, array $nextPhaseNumbers)
  {
    $next_phase_numbers = [];
    foreach ($phase->getNextQualificationSystems() as $system) {
      /** @noinspection PhpUnhandledExceptionInspection */
      // all systems in the array have a next phase and the phase has a phase number
      $next_phase_numbers[$system->getNextPhase()->getPhaseNumber()] = false;
    }
    foreach ($nextPhaseNumbers as $phase_number) {
      if ($phase_number <= $phase->getPhaseNumber()) {
        throw new UnorderedPhaseNumberException($phase->getPhaseNumber(), $phase_number);
      }
      if (array_key_exists($phase_number, $next_phase_numbers)) {
        if ($next_phase_numbers[$phase_number] == true) {
          //duplicate next phase number!
          throw new DuplicateException($phase_number, 'next phase number',
            'the next phase numbers list of phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
      } else {
        if (!$phase->getCompetition()->getPhases()->containsKey($phase_number)) {
          throw new ReferenceException($phase_number, "phase numbers in competition " .
            $phase->getCompetition()->getName());
        }
        $system = new QualificationSystem();
        $next_phase = $phase->getCompetition()->getPhases()[$phase_number];
        $system->setNextPhase($next_phase);
        $system->setPreviousPhase($phase);
        $this->em->persist($system);
      }
      $next_phase_numbers[$phase_number] = true;
    }
    foreach ($phase->getNextQualificationSystems()->toArray() as $system) {
      /** @var $system QualificationSystem */
      /** @noinspection PhpUnhandledExceptionInspection */
      // all systems in the array have a next phase and the phase has a phase number
      if ($next_phase_numbers[$system->getNextPhase()->getPhaseNumber()] === false) {
        $phase->getNextQualificationSystems()->removeElement($system);
        /** @noinspection PhpUnhandledExceptionInspection */
        // all systems in the array have a next phase and the phase has a phase number
        $system->getNextPhase()->getPreviousQualificationSystems()->removeElement($system);
        $this->em->remove($system);
      }
    }
  }

  /**
   * Replaces the rankings of the given phase according to the request
   * @param Phase $phase the phase to modify
   * @param mixed[] $values the request values for the rankings
   * @throws DuplicateException a unique rank is occurring twice or a team start number is specified twice for this
   *                            ranking
   * @throws \App\Exceptions\ValueNotSet the phase has no phase number or no competition or its competition has no name
   * @throws ReferenceException a referenced team start number in the team values does not exist
   */
  private function replaceRankings(Phase $phase, array $values)
  {
    $unique_ranks = [];
    foreach ($phase->getRankings()->getKeys() as $key) {
      $unique_ranks[$key] = false;
    }
    $team_start_numbers = [];
    foreach ($values as $ranking_values) {
      if (array_key_exists($ranking_values['uniqueRank'], $unique_ranks)) {
        if ($unique_ranks[$ranking_values['uniqueRank']] == true) {
          //duplicate unique rank!
          throw new DuplicateException($ranking_values['uniqueRank'], 'unique rank',
            'the ranking list of phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
        $ranking = $phase->getRankings()->get($ranking_values['uniqueRank']);
        $this->setFromSpecification($ranking, $this->rankingSpecification, $ranking_values);
      } else {
        $ranking = new Ranking();
        $this->setFromSpecification($ranking, $this->rankingSpecification, $ranking_values);
        /** @noinspection PhpUnhandledExceptionInspection */ // the ranking has a unique rank since its required in the
        // request
        $ranking->setPhase($phase);
        $this->em->persist($ranking);
      }
      $unique_ranks[$ranking_values['uniqueRank']] = true;
      //the ranking has a phase and a unique rank since it is required for the request
      $this->replaceRankingTeams($ranking, $ranking_values['teamStartNumbers'], $team_start_numbers);
    }
    foreach ($unique_ranks as $key => $used) {
      if (!$used) {
        $ranking = $phase->getRankings()->get($key);
        $phase->getRankings()->remove($key);
        $this->removeRanking($ranking);
      }
    }
  }

  /**
   * Replaces the matches of the given phase according to the request
   * @param Phase $phase the phase to modify
   * @param mixed[] $values the request values for the matches
   * @throws DuplicateException a match number is occurring twice or a unique rank is specified twice for the teams in
   *                            this match or a game number is occurring twice or a player id is specified twice for the
   *                            players of a game
   * @throws \App\Exceptions\ValueNotSet the phase has no phase number or no competition or its competition has no name
   * @throws ReferenceException A unique rank is in one of the rankings lists which is not a valid unique rank for this
   *                            phase or a player of a team is not in the players lists of this team
   */
  private function replaceMatches(Phase $phase, array $values)
  {
    $match_numbers = [];
    foreach ($phase->getMatches()->getKeys() as $key) {
      $match_numbers[$key] = false;
    }
    foreach ($values as $match_values) {
      if (array_key_exists($match_values['matchNumber'], $match_numbers)) {
        if ($match_numbers[$match_values['matchNumber']] == true) {
          //duplicate match number!
          throw new DuplicateException($match_values['matchNumber'], 'match number',
            'the match list of phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
        $match = $phase->getMatches()->get($match_values['matchNumber']);
        $this->setFromSpecification($match, $this->matchSpecification, $match_values);
      } else {
        $match = new Match();
        $this->setFromSpecification($match, $this->matchSpecification, $match_values);
        /** @noinspection PhpUnhandledExceptionInspection */ // the match has a match number since its required in the
        // request
        $match->setPhase($phase);
        $this->em->persist($match);
        foreach ($this->rankingSystems[Level::MATCH] as $ranking_system) {
          $match->getRankingSystems()->set($ranking_system->getId(), $ranking_system);
          $ranking_system->getMatches()->set($match->getId(), $match);
        }
      }
      $match_numbers[$match_values['matchNumber']] = true;

      //the ranking has a phase and a unique rank since it is required for the request
      $unique_ranks = [];
      $this->replaceMatchRankings($match, $match_values['rankingsAUniqueRanks'], 'A', $unique_ranks);
      $this->replaceMatchRankings($match, $match_values['rankingsBUniqueRanks'], 'B', $unique_ranks);
      // the match has a match number and a phase since it is required for the request
      $this->replaceGames($match, $match_values['games']);
    }
    foreach ($match_numbers as $key => $used) {
      if (!$used) {
        $match = $phase->getMatches()->get($key);
        $phase->getMatches()->remove($key);
        $this->removeMatch($match);
      }
    }
  }

  /**
   * Replaces the games of the given match according to the request
   * @param Match $match the match to modify
   * @param mixed[] $values the request values for the games
   * @throws DuplicateException A game number is occurring twice or a player id is specified twice for the players of
   *                            this game
   * @throws \App\Exceptions\ValueNotSet the match has no match number or no phase or its phase has no phase number or
   *                                     no competition or its competition has no name
   * @throws ReferenceException a player of a team is not in the players lists of this team
   */
  private function replaceGames(Match $match, array $values)
  {
    $game_numbers = [];
    foreach ($match->getGames()->getKeys() as $key) {
      $game_numbers[$key] = false;
    }
    foreach ($values as $game_values) {
      if (array_key_exists($game_values['gameNumber'], $game_numbers)) {
        if ($game_numbers[$game_values['gameNumber']] == true) {
          //duplicate match number!
          $phase = $match->getPhase();
          throw new DuplicateException($game_values['gameNumber'], 'game number',
            'the game list of match ' . $match->getMatchNumber() . ' of phase ' . $phase->getPhaseNumber() .
            ' of the competition ' . $phase->getCompetition()->getName());
        }
        $game = $match->getGames()->get($game_values['gameNumber']);
        $this->setFromSpecification($game, $this->gameSpecification, $game_values);
      } else {
        $game = new Game();
        $this->setFromSpecification($game, $this->gameSpecification, $game_values);
        /** @noinspection PhpUnhandledExceptionInspection */ // the game has a game number since its required in the
        // request
        $game->setMatch($match);
        $this->em->persist($game);
        foreach ($this->rankingSystems[Level::GAME] as $ranking_system) {
          $game->getRankingSystems()->set($ranking_system->getId(), $ranking_system);
          $ranking_system->getGames()->set($game->getId(), $game);
        }
      }
      $game_numbers[$game_values['gameNumber']] = true;

      //the game has a match and a game number since it is required for the request
      $player_ids = [];
      $this->replaceGamePlayers($game, $game_values['playersA'], 'A', $player_ids);
      $this->replaceGamePlayers($game, $game_values['playersB'], 'B', $player_ids);
    }
    foreach ($game_numbers as $key => $used) {
      if (!$used) {
        $game = $match->getGames()->get($key);
        $match->getGames()->remove($key);
        $this->removeGame($game);
      }
    }
  }

  /**
   * Replaces the game players of the given game according to the request
   * @param Game $game the game to modify
   * @param int[] $player_ids the list of the player ids
   * @param string $team_letter the team letter to modify (A or B)
   * @param bool[] $other_player_ids the player ids of the other team already parsed
   * @throws DuplicateException Either a duplicate player id in this list or a player id in the list which is
   *                            also in $other_player_ids.
   * @throws \App\Exceptions\ValueNotSet the game has no game number or no match or its match has no match number or no
   *                                     phase or its phase has no phase number or no competition or its competition has
   *                                     no name
   * @throws ReferenceException a player for this team is not in the players lists of this team
   */
  private function replaceGamePlayers(Game $game, array $player_ids, string $team_letter, array &$other_player_ids)
  {
    $internal_other_player_ids = [];
    $method = 'getPlayers' . $team_letter;

    /** @var Collection|Player[] $players */
    $players = $game->$method();
    foreach ($players->getKeys() as $id) {
      $internal_other_player_ids[$id] = false;
    }

    $match = $game->getMatch();
    $phase = $match->getPhase();
    foreach ($player_ids as $id) {
      if (array_key_exists($id, $internal_other_player_ids) && $internal_other_player_ids[$id] == true) {
        //duplicate player!
        throw new DuplicateException($id, 'player id',
          'the players ' . $team_letter . ' list of the game with game number ' . $game->getGameNumber() .
          ' of the match with match number ' . $match->getMatchNumber() . ' of the phase ' . $phase->getPhaseNumber() .
          ' of the competition ' . $phase->getCompetition()->getName());
      } else if (array_key_exists($id, $other_player_ids)) {
        //duplicate player!
        throw new DuplicateException($id, 'player id',
          'the players A and players B lists of the game with game number ' . $game->getGameNumber() .
          ' of the match with match number ' . $match->getMatchNumber() . ' of the phase ' . $phase->getPhaseNumber() .
          ' of the competition ' . $phase->getCompetition()->getName());
      }
      if (!array_key_exists($id, $internal_other_player_ids)) {
        $found = false;
        $rankings_method = 'getRankings' . $team_letter;
        /** @var Ranking[] $rankings */
        $rankings = $game->getMatch()->$rankings_method();
        foreach ($rankings as $ranking) {
          foreach ($ranking->getTeams() as $team) {
            if ($team->getPlayers()->containsKey($id)) {
              $found = true;
              break;
            }
          }
          if ($found) {
            break;
          }
        }
        if (!$found) {
          throw new ReferenceException($id, "player id in players " . $team_letter . " in game " .
            $game->getGameNumber() . " of match " . $match->getMatchNumber() . " of phase " . $phase->getPhaseNumber() .
            " of competition " . $phase->getCompetition()->getName() . ", which is not in the players lists of the "
            . "teams of team " . $team_letter);
        }
        $players->set($id, $this->em->find(Player::class, $id));
      }
      $other_player_ids[$id] = true;
      $internal_other_player_ids[$id] = true;
    }
    foreach ($internal_other_player_ids as $id => $used) {
      if (!$used) {
        $players->remove($id);
      }
    }
  }

  /**
   * Replaces the teams of the given ranking according to the request
   * @param Ranking $ranking the ranking to modify
   * @param array $team_values the list of the teams start numbers
   * @param string[] $other_team_start_numbers team start numbers of other rankings
   * @throws DuplicateException a team start number is specified twice for this ranking
   * @throws \App\Exceptions\ValueNotSet the ranking has no phase or unique rank or its phase has no phase number or no
   *                                     competition or its competition has no name
   * @throws ReferenceException a referenced team start number in the team values does not exist
   */
  private function replaceRankingTeams(Ranking $ranking, array $team_values, array &$other_team_start_numbers)
  {
    $team_start_numbers = [];
    foreach ($ranking->getTeams()->getKeys() as $start_number) {
      $team_start_numbers[$start_number] = false;
    }
    foreach ($team_values as $start_number) {
      $phase = $ranking->getPhase();
      if (array_key_exists($start_number, $team_start_numbers) && $team_start_numbers[$start_number] == true) {
        //duplicate team!
        throw new DuplicateException($start_number, 'team start number',
          'the team list of ranking ' . $ranking->getName() . '(Unique rank ' .
          $ranking->getUniqueRank() . ') of the phase ' . $phase->getPhaseNumber() . ' of the competition ' .
          $phase->getCompetition()->getName());
      } else if (array_key_exists($start_number, $other_team_start_numbers)) {
        throw new DuplicateException($start_number, 'team start number',
          'the team start number lists of the ranking of the phase ' . $phase->getPhaseNumber() .
          ' of the competition ' . $phase->getCompetition()->getName());
      }
      if (!array_key_exists($start_number, $team_start_numbers)) {
        if (!$phase->getCompetition()->getTeams()->containsKey($start_number)) {
          throw new ReferenceException($start_number, "team start number in competition " .
            $phase->getCompetition()->getName());
        }
        $ranking->getTeams()->set($start_number, $phase->getCompetition()->getTeams()->get($start_number));
      }
      $other_team_start_numbers[$start_number] = true;
      $team_start_numbers[$start_number] = true;
    }
    foreach ($team_start_numbers as $start_number => $used) {
      if (!$used) {
        $ranking->getTeams()->remove($start_number);
      }
    }
  }

  /**
   * Replaces the match rankings of the given match according to the request
   * @param Match $match the match to modify
   * @param int[] $rankings_values the list of the unique rankings
   * @param string $team_letter the team letter to modify (A or B)
   * @param bool[] $u_ranks the ranks of the other team already parsed
   * @throws DuplicateException Either a duplicate unique rank in this list or a unique rank in the list which is
   *                            also in u_ranks.
   * @throws ReferenceException A unique rank is in the list which is not a valid unique rank for this phase
   * @throws \App\Exceptions\ValueNotSet the match has no phase set or no match number or its phase has no phase number
   *                                      or no competition or its competition has no name
   */
  private function replaceMatchRankings(Match $match, array $rankings_values, string $team_letter, array &$u_ranks)
  {
    $unique_ranks = [];
    $method = 'getRankings' . $team_letter;

    /** @var Collection|Ranking[] $rankings */
    $rankings = $match->$method();
    foreach ($rankings->getKeys() as $unique_rank) {
      $unique_ranks[$unique_rank] = false;
    }

    $phase = $match->getPhase();
    foreach ($rankings_values as $unique_rank) {
      if (array_key_exists($unique_rank, $unique_ranks)) {
        if ($unique_ranks[$unique_rank] == true) {
          //duplicate rank!
          throw new DuplicateException($unique_rank, 'unique rank',
            'the rankings ' . $team_letter . ' list of the match with match number ' .
            $match->getMatchNumber() . ' of the phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
      } else if (array_key_exists($unique_rank, $u_ranks)) {
        //duplicate rank!
        throw new DuplicateException($unique_rank, 'unique rank',
          'the rankings A and ranking B lists of the match with match number ' . $match->getMatchNumber() .
          ' of the phase ' . $phase->getPhaseNumber() . ' of the competition ' . $phase->getCompetition()->getName());
      } else {
        if (!$phase->getRankings()->containsKey($unique_rank)) {
          throw new ReferenceException($unique_rank, 'unique rank in phase ' . $phase->getPhaseNumber() .
            ' of the competition ' . $phase->getCompetition()->getName());
        }
        $rankings->set($unique_rank, $phase->getRankings()[$unique_rank]);
      }

      $u_ranks[$unique_rank] = true;

      $unique_ranks[$unique_rank] = true;
    }
    foreach ($unique_ranks as $unique_rank => $used) {
      if (!$used) {
        $rankings->remove($unique_rank);
      }
    }
  }

  /** @noinspection PhpDocMissingThrowsInspection */
  /**
   * Replaces the players of the given team according to the request
   * @param Team $team the team to modify
   * @param mixed[] $player_values the request values for the players
   * @throws DuplicateException a player is specified twice for this team
   */
  private function replaceTeamPlayers(Team $team, array $player_values)
  {
    $player_ids = [];
    foreach ($team->getPlayers()->getKeys() as $id) {
      $player_ids[$id] = false;
    }
    foreach ($player_values as $player_id) {
      if (array_key_exists($player_id, $player_ids)) {
        if ($player_ids[$player_id] == true) {
          //duplicate player!
          throw new DuplicateException($player_id, 'player id',
            'the player list of team ' . $team->getName());
        }
      } else {
        $team->getPlayers()->set($player_id, $this->em->find(Player::class, $player_id));
      }
      $player_ids[$player_id] = true;
    }
    foreach ($player_ids as $id => $used) {
      if (!$used) {
        $team->getPlayers()->remove($id);
      }
    }
  }

  /**
   * Replaces the teams of the given competition according to the request
   * @param Competition $competition the competition to modify
   * @param mixed[] $values the request values for the teams
   * @throws DuplicateException a team start number is occurring twice or a player is specified twice for a team
   * @throws \App\Exceptions\ValueNotSet the competition has no name
   */
  private function replaceTeams(Competition $competition, array $values)
  {
    $old_start_numbers = [];
    foreach ($competition->getTeams() as $team) {
      /** @noinspection PhpUnhandledExceptionInspection */ //all teams in the array have a start number
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
        /** @noinspection PhpUnhandledExceptionInspection */ // the team has a start number since it is required in the
        // request
        $team->setCompetition($competition);
        $this->em->persist($team);
      }
      $old_start_numbers[$team_values['startNumber']] = true;
      $this->replaceTeamPlayers($team, $team_values['players']);
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
