<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/14/18
 * Time: 11:51 AM
 */

namespace App\Service\AsyncServices;

use App\Entity\AsyncRequest;
use App\Entity\Competition;
use App\Entity\Game;
use App\Entity\Match;
use App\Entity\Phase;
use App\Entity\Player;
use App\Entity\QualificationSystem;
use App\Entity\Ranking;
use App\Entity\RankingSystem;
use App\Entity\Team;
use App\Entity\TeamMembership;
use App\Entity\Tournament;
use App\Entity\User;
use App\Exceptions\GameHasMissingModes;
use App\Exceptions\ManualValidationException;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Tfboe\FmLib\Entity\Categories\GameMode;
use Tfboe\FmLib\Entity\Categories\OrganizingMode;
use Tfboe\FmLib\Entity\Categories\ScoreMode;
use Tfboe\FmLib\Entity\Categories\Table;
use Tfboe\FmLib\Entity\Categories\TeamMode;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\Helpers\Result;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\TeamMembershipInterface;
use Tfboe\FmLib\Entity\TournamentInterface;
use Tfboe\FmLib\Exceptions\DuplicateException;
use Tfboe\FmLib\Exceptions\ReferenceException;
use Tfboe\FmLib\Exceptions\UnorderedPhaseNumberException;
use Tfboe\FmLib\Helpers\Level;
use Tfboe\FmLib\Helpers\Tools;
use Tfboe\FmLib\Service\LoadingServiceInterface;

/**
 * Class CreateOrReplaceTournament
 * @package App\Service\AsyncServices
 */
class CreateOrReplaceTournament implements CreateOrReplaceTournamentInterface
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

  /**
   * @var EntityManagerInterface
   */
  private $em;

  /**
   * @var LoadingServiceInterface
   */
  private $ls;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * CreateOrReplaceTournament constructor.
   * @param EntityManagerInterface $em
   * @param LoadingServiceInterface $ls
   */
  public function __construct(EntityManagerInterface $em, LoadingServiceInterface $ls)
  {
    $this->em = $em;
    $this->ls = $ls;
  }
//</editor-fold desc="Constructor">


//<editor-fold desc="Public Methods">
  /**
   * @inheritdoc
   * @throws DuplicateException
   * @throws GameHasMissingModes
   * @throws ManualValidationException
   * @throws ReferenceException
   * @throws UnorderedPhaseNumberException
   */
  public function run($input, $reportProgress): array
  {
    //manual validation
    $current = "request body";
    $this->checkRequired($input, 'user', $current);
    $this->checkRequired($input, 'userIdentifier', $current);
    $this->checkString($input, 'userIdentifier', $current);
    $this->checkRequired($input, 'name', $current);
    $this->checkString($input, 'name', $current);
    $this->checkString($input, 'tournamentListId', $current);
    $this->checkRequired($input, 'competitions', $current);
    $this->checkArray($input, 'competitions', $current, 1);
    $this->checkBoolean($input, 'finished', $current);
    $this->checkCategories($input, $current);
    $this->checkTimes($input, $current);

    $competitionNames = [];
    $playerIds = [];
    foreach ($input['competitions'] as $key => $competition) {
      $current = "competitions[$key]";
      $this->checkRequired($competition, 'name', $current);
      $this->checkString($competition, 'name', $current);
      $this->checkDistinct($competition, 'name', $current, $competitionNames);
      $this->checkPresent($competition, 'teams', $current);
      $this->checkArray($competition, 'teams', $current);
      $this->checkArray($competition, 'phases', $current);
      $this->checkCategories($competition, $current);
      $this->checkTimes($competition, $current);
      foreach ($competition['teams'] as $k2 => $team) {
        $teamDesc = $current . "[teams][$k2]";
        $this->checkString($team, 'name', $teamDesc);
        $this->checkRequired($team, 'rank', $teamDesc);
        $this->checkInteger($team, 'rank', $teamDesc);
        $this->checkRequired($team, 'startNumber', $teamDesc);
        $this->checkInteger($team, 'startNumber', $teamDesc, 1);
        $this->checkPresent($team, 'players', $teamDesc);
        $this->checkArray($team, 'players', $teamDesc);
        foreach ($team['players'] as $id) {
          $playerIds[$id] = true;
        }
      }
      if (array_key_exists('phases', $competition)) {
        foreach ($competition['phases'] as $k2 => $phase) {
          $phaseDesc = $current . "[phases][$k2]";
          $this->checkRequired($phase, 'phaseNumber', $phaseDesc);
          $this->checkInteger($phase, 'phaseNumber', $phaseDesc, -1);
          $this->checkString($phase, 'name', $phaseDesc);
          $this->checkArray($phase, 'nextPhaseNumbers', $phaseDesc);
          if (array_key_exists('nextPhaseNumbers', $phase)) {
            foreach ($phase['nextPhaseNumbers'] as $k3 => $number) {
              $this->checkInteger($phase['nextPhaseNumbers'], $k3, $phaseDesc . "[nextPhaseNumbers]", 0);
            }
          }
          $this->checkPresent($phase, 'rankings', $phaseDesc);
          $this->checkArray($phase, 'rankings', $phaseDesc);
          $this->checkPresent($phase, 'matches', $phaseDesc);
          $this->checkArray($phase, 'matches', $phaseDesc);
          $this->checkCategories($phase, $phaseDesc);
          $this->checkTimes($phase, $phaseDesc);
          foreach ($phase['rankings'] as $k3 => $ranking) {
            $rankingDesc = $phaseDesc . "[rankings][$k3]";
            $this->checkRequired($ranking, 'uniqueRank', $rankingDesc);
            $this->checkInteger($ranking, 'uniqueRank', $rankingDesc, 1);
            $this->checkRequired($ranking, 'teamStartNumbers', $rankingDesc);
            $this->checkArray($ranking, 'teamStartNumbers', $rankingDesc, 1);
            foreach ($ranking['teamStartNumbers'] as $k4 => $number) {
              $this->checkInteger($ranking['teamStartNumbers'], $k4, $rankingDesc . "[teamStartNumbers]", 1);
            }
            $this->checkString($ranking, 'name', $rankingDesc);
            $this->checkRequired($ranking, 'rank', $rankingDesc);
            $this->checkInteger($ranking, 'rank', $rankingDesc, 1);
          }
          foreach ($phase['matches'] as $k3 => $match) {
            $matchDesc = $phaseDesc . "[matches][$k3]";
            $this->checkRequired($match, 'matchNumber', $matchDesc);
            $this->checkInteger($match, 'matchNumber', $matchDesc, 1);
            $this->checkRequired($match, 'rankingsAUniqueRanks', $matchDesc);
            $this->checkArray($match, 'rankingsAUniqueRanks', $matchDesc, 1);
            foreach ($match['rankingsAUniqueRanks'] as $k4 => $number) {
              $this->checkInteger($match['rankingsAUniqueRanks'], $k4, $matchDesc . "[rankingsAUniqueRanks]", 1);
            }
            $this->checkRequired($match, 'rankingsBUniqueRanks', $matchDesc);
            $this->checkArray($match, 'rankingsBUniqueRanks', $matchDesc, 1);
            foreach ($match['rankingsBUniqueRanks'] as $k4 => $number) {
              $this->checkInteger($match['rankingsBUniqueRanks'], $k4, $matchDesc . "[rankingsBUniqueRanks]", 1);
            }
            $this->checkPresent($match, 'games', $matchDesc);
            $this->checkArray($match, 'games', $matchDesc);
            $this->checkCategories($match, $matchDesc);
            $this->checkTimes($match, $matchDesc);
            $this->checkResults($match, $matchDesc);
            foreach ($match['games'] as $k4 => $game) {
              $gameDesc = $matchDesc . "[games][$k4]";
              $this->checkCategories($game, $gameDesc);
              $this->checkTimes($game, $gameDesc);
              $this->checkResults($game, $gameDesc);
              $this->checkRequired($game, 'gameNumber', $gameDesc);
              $this->checkInteger($game, 'gameNumber', $gameDesc, 1);
              $this->checkPresent($game, 'playersA', $gameDesc);
              $this->checkArray($game, 'playersA', $gameDesc, $game['played'] ? 1 : 0);
              foreach ($game['playersA'] as $id) {
                $playerIds[$id] = true;
              }
              $this->checkPresent($game, 'playersB', $gameDesc);
              $this->checkArray($game, 'playersB', $gameDesc, $game['played'] ? 1 : 0);
              foreach ($game['playersB'] as $id) {
                $playerIds[$id] = true;
              }
            }
          }
        }
      }
    }

    $this->tournamentSpecification = [
      'userIdentifier' => ['validation' => 'required|string'],
      'name' => ['validation' => 'required|string'],
      'tournamentListId' => ['validation' => 'string', 'default' => ''],
      'competitions' => ['validation' => 'required|array|min:1', 'ignore' => True],
      'finished' => ['validation' => 'boolean', 'default' => true]
    ];
    $this->tournamentSpecification = array_merge(
      $this->tournamentSpecification, $this->categoriesSpecifications(''),
      $this->timeSpecifications(''));

    $this->competitionSpecification = [
      'competitions.*.name' => ['validation' => 'required|string|distinct'],
      'competitions.*.teams' => ['validation' => 'present|array', 'ignore' => True],
      'competitions.*.phases' => ['validation' => 'array', 'ignore' => True]
    ];
    $this->competitionSpecification = array_merge($this->competitionSpecification,
      $this->categoriesSpecifications('competitions.*.'),
      $this->timeSpecifications('competitions.*.'));

    $this->teamSpecification = [
      'competitions.*.teams.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.teams.*.rank' => ['validation' => 'required|integer'],
      'competitions.*.teams.*.startNumber' => ['validation' => 'required|integer|min:1'],
      'competitions.*.teams.*.players' => ['validation' => 'present|array', 'ignore' => True],
      'competitions.*.teams.*.players.*' => ['ignore' => True],
    ];

    $this->phaseSpecification = [
      'competitions.*.phases.*.phaseNumber' => ['validation' => 'required|integer|min:-1'],
      'competitions.*.phases.*.name' => ['validation' => 'string', 'default' => ''],
      'competitions.*.phases.*.nextPhaseNumbers' => ['validation' => 'array', 'ignore' => True],
      'competitions.*.phases.*.nextPhaseNumbers.*' => ['validation' => 'integer|min:0', 'ignore' => True],
      'competitions.*.phases.*.rankings' => ['validation' => 'present|array', 'ignore' => True],
      'competitions.*.phases.*.matches' => ['validation' => 'present|array', 'ignore' => True]
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
      'competitions.*.phases.*.matches.*.games' => ['validation' => 'present|array', 'ignore' => True],
    ];

    $this->matchSpecification = array_merge($this->matchSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.matches.*.'),
      $this->resultSpecifications('competitions.*.phases.*.matches.*.'),
      $this->timeSpecifications('competitions.*.phases.*.matches.*.'));

    $this->gameSpecification = [
      'competitions.*.phases.*.matches.*.games.*.gameNumber' => ['validation' => 'required|integer|min:1'],
      'competitions.*.phases.*.matches.*.games.*.playersA' =>
        ['validation' => 'present|array|required_if:competitions.*.phases.*.matches.*.games.*.played,true',
          'ignore' => True],
      'competitions.*.phases.*.matches.*.games.*.playersA.*' =>
        ['ignore' => True],
      'competitions.*.phases.*.matches.*.games.*.playersB' =>
        ['validation' => 'present|array|required_if:competitions.*.phases.*.matches.*.games.*.played,true',
          'ignore' => True],
      'competitions.*.phases.*.matches.*.games.*.playersB.*' =>
        ['ignore' => True]
    ];

    $this->gameSpecification = array_merge($this->gameSpecification,
      $this->categoriesSpecifications('competitions.*.phases.*.matches.*.games.*.'),
      $this->resultSpecifications('competitions.*.phases.*.matches.*.games.*.'),
      $this->timeSpecifications('competitions.*.phases.*.matches.*.games.*.'));

    //check if each game has a descendant for each mode
    $this->checkModes($input);

    $result = $this->doCreateOrReplaceTournament($input, $reportProgress);
    //start process to recalculate rankings
    $asyncRequest = new AsyncRequest(["input" => []], RecalculateRankingSystemsInterface::class);
    $this->em->persist($asyncRequest);
    $this->em->flush();
    dispatch($asyncRequest->getId());
    //$this->aes->runBashCommand(env('PHP_COMMAND', 'php') . ' ../artisan recompute-rankings');
    //$aes->runBashCommand('pwd >> /tmp/test');
    return ['data' => ['type' => $result], 'status' => 200];
  }

//<editor-fold desc="Private Methods">

  /**
   * Checks if input has set one of the 5 modes and if it has adds it to the active modes array and returns it
   * @param array $activeModes the input array of active modes
   * @param array $input the input array
   * @return mixed the modified active modes array
   */
  private function addActiveModes($activeModes, $input)
  {
    $modes = ['gameMode', 'organizingMode', 'scoreMode', 'table', 'teamMode'];
    foreach ($modes as $mode) {
      if (array_key_exists($mode, $input) && $input[$mode] !== null) {
        $activeModes[$mode] = true;
      }
    }
    return $activeModes;
  }

  /**
   * @param TournamentHierarchyEntity $entity
   * @param $rankingSystem
   */
  private function addInfluencingRankingSystem(TournamentHierarchyEntity $entity, $rankingSystem)
  {
    if ($entity->addInfluencingRankingSystem($rankingSystem) && $entity->getParent() !== null) {
      $this->addInfluencingRankingSystem($entity, $rankingSystem);
    }
  }

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
   * @param $data
   * @param $property
   * @param $dataName
   * @param int $minElements
   * @throws ManualValidationException
   */
  private function checkArray($data, $property, $dataName, $minElements = 0)
  {
    if (array_key_exists($property, $data)) {
      if (!is_array($data[$property])) {
        throw new ManualValidationException("The property $property in $dataName must be an array");
      }
      if (count($data[$property]) < $minElements) {
        throw new ManualValidationException(
          "The property $property in $dataName must be an array with at least $minElements elements");
      }
    }
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkBoolean($data, $property, $dataName)
  {
    if (array_key_exists($property, $data)) {
      if (!is_bool($data[$property])) {
        throw new ManualValidationException("The property $property in $dataName must be a boolean");
      }
    }
  }

  /**
   * @param $data
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkCategories($data, $dataName)
  {
    $this->checkEnum($data, 'gameMode', $dataName, GameMode::class);
    $this->checkEnum($data, 'organizingMode', $dataName, OrganizingMode::class);
    $this->checkEnum($data, 'scoreMode', $dataName, ScoreMode::class);
    $this->checkEnum($data, 'table', $dataName, Table::class);
    $this->checkEnum($data, 'teamMode', $dataName, TeamMode::class);
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @param null $format
   * @throws ManualValidationException
   */
  private function checkDate($data, $property, $dataName, $format = null)
  {
    if (array_key_exists($property, $data)) {
      if ($format === null) {
        $format = Tools::getDatetimetzFormat();
      }
      if (!is_string($data[$property])) {
        throw new ManualValidationException("The property $property in $dataName must be a string representing a date");
      }
      if (\DateTime::createFromFormat($format, $data[$property]) === false) {
        throw new ManualValidationException("The property $property in $dataName must be a date");
      }
    }
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @param $otherValues
   * @throws ManualValidationException
   */
  private function checkDistinct($data, $property, $dataName, &$otherValues)
  {
    if (array_key_exists($property, $data)) {
      if (array_key_exists($data[$property], $otherValues)) {
        throw new ManualValidationException(
          "The property $property in $dataName must be distinct");
      } else {
        $otherValues[$data[$property]] = true;
      }
    }
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @param $enumClass
   * @throws ManualValidationException
   */
  private function checkEnum($data, $property, $dataName, $enumClass)
  {
    if (array_key_exists($property, $data)) {
      $this->checkString($data, $property, $dataName);
      if (!call_user_func($enumClass . '::isValidName', $data[$property], true)) {
        throw new ManualValidationException(
          "The property $property in $dataName must be an enum value of class $enumClass");
      }
    }
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @param null $min
   * @throws ManualValidationException
   */
  private function checkInteger($data, $property, $dataName, $min = null)
  {
    if (array_key_exists($property, $data)) {
      if (!is_integer($data[$property])) {
        throw new ManualValidationException("The property $property in $dataName must be an integer");
      }
      if ($min !== null && $data[$property] < $min) {
        throw new ManualValidationException(
          "The property $property in $dataName must be at least $min");
      }
    }
  }

  /**
   * Checks if every game has at least one predecessor who set the mode for each of the 5 modes
   * @param array $input
   * @throws GameHasMissingModes at least one game has a missing mode
   */
  private function checkModes($input)
  {
    $activeModes = $this->addActiveModes([], $input);
    if (count($activeModes) == 5) {
      return;
    }
    foreach ($input['competitions'] as $competition) {
      $compActiveModes = $this->addActiveModes($activeModes, $competition);
      if (count($compActiveModes) == 5) {
        continue;
      }
      foreach ($competition['phases'] as $phase) {
        $phaseActiveModes = $this->addActiveModes($compActiveModes, $phase);
        if (count($phaseActiveModes) == 5) {
          continue;
        }
        foreach ($phase['matches'] as $match) {
          $matchActiveMode = $this->addActiveModes($phaseActiveModes, $match);
          if (count($matchActiveMode) == 5) {
            continue;
          }
          foreach ($match['games'] as $game) {
            $gameActiveMode = $this->addActiveModes($matchActiveMode, $game);
            if (count($gameActiveMode) < 5) {
              throw new GameHasMissingModes("The game with number " . $game['gameNumber'] .
                " misses some modes, the following modes are given: "
                . implode(", ", array_keys($gameActiveMode)));
            }
          }
        }
      }
    }
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkPresent($data, $property, $dataName)
  {
    if (!array_key_exists($property, $data)) {
      throw new ManualValidationException("The property $property is required in $dataName");
    }
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkRequired($data, $property, $dataName)
  {
    $this->checkPresent($data, $property, $dataName);
    if ($data[$property] === "" || (is_array($data[$property]) && count($data[$property]) === 0)) {
      throw new ManualValidationException("The property $property in $dataName must be non-empty");
    }
  }

  /**
   * @param $data
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkResults($data, $dataName)
  {
    $this->checkRequired($data, 'resultA', $dataName);
    $this->checkInteger($data, 'resultA', $dataName, 0);
    $this->checkRequired($data, 'resultB', $dataName);
    $this->checkInteger($data, 'resultB', $dataName, 0);
    $this->checkRequired($data, 'result', $dataName);
    $this->checkEnum($data, 'result', $dataName, Result::class);
    $this->checkRequired($data, 'played', $dataName);
    $this->checkBoolean($data, 'played', $dataName);
  }

  /**
   * @param $data
   * @param $property
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkString($data, $property, $dataName)
  {
    if (array_key_exists($property, $data)) {
      if (!is_string($data[$property])) {
        throw new ManualValidationException("The property $property in $dataName must be a string");
      }
    }
  }

  /**
   * @param $data
   * @param $dataName
   * @throws ManualValidationException
   */
  private function checkTimes($data, $dataName)
  {
    $this->checkDate($data, 'startTime', $dataName);
    $this->checkDate($data, 'endTime', $dataName);
  }

  /**
   * creates or replaces the tournament as specified in the request
   * @param mixed $input request input
   * @param $reportProgress
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
  private function doCreateOrReplaceTournament($input, $reportProgress): string
  {
    assert($input['user'] != null);
    /** @var Tournament|null $tournament */
    $tournament = $this->em->getRepository(Tournament::class)->findOneBy(
      ['userIdentifier' => $input['userIdentifier'], 'creator' => $input['user']]);

    $type = 'replace';

    /** @var RankingSystem[] $rankingSystems */
    $rankingSystems = $this->em->getRepository(RankingSystem::class)->findAll();
    $this->rankingSystems = [];
    foreach (Level::getValues() as $value) {
      $this->rankingSystems[$value] = [];
    }
    foreach ($rankingSystems as $rankingSystem) {
      if ($rankingSystem->getDefaultForLevel() !== null) {
        $this->rankingSystems[$rankingSystem->getDefaultForLevel()][] = $rankingSystem;
      }
    }

    if ($tournament == null) {
      $tournament = new Tournament();
      /** @var User $user */
      $user = $this->em->find(User::class, $input['user']);
      $tournament->setCreator($user);
      $this->em->persist($tournament);
      foreach ($this->rankingSystems[Level::TOURNAMENT] as $rankingSystem) {
        $tournament->getRankingSystems()->set($rankingSystem->getId(), $rankingSystem);
        $this->addInfluencingRankingSystem($tournament, $rankingSystem);
      }
      $type = 'create';
    }

    Tools::setFromSpecification($tournament, $this->tournamentSpecification, $input, $this->em);
    $this->replaceCompetitions($input, $tournament, $reportProgress);
    $this->em->flush();

    return $type;
  }

  /**
   * Gets a transformation function which transforms an enum name into the corresponding value
   * @param string $enumName the name of the enum
   * @return \Closure the function which transforms a name into the enum value
   */
  private function enumTransformer(string $enumName): \Closure
  {
    return function ($name) use ($enumName) {
      return call_user_func([$enumName, "getValue"], $name);
    };
  }

  private function flushAndForgetEntities()
  {
    $this->em->flush();
    static $toForgetClasses = null;
    static $tournamentHierarchyEntity = null;
    if ($toForgetClasses === null) {
      $toForgetClasses = [Team::class, Phase::class,
        TeamMembership::class, Player::class, QualificationSystem::class, Ranking::class,
        Match::class, Game::class];
      foreach ($toForgetClasses as &$class) {
        $class = $this->em->getClassMetadata($class)->getReflectionClass()->getName();
      }
      $tournamentHierarchyEntity = $this->em->getClassMetadata(TournamentHierarchyEntity::class)
        ->getReflectionClass()->getName();
    }

    $identityMap = $this->em->getUnitOfWork()->getIdentityMap();
    foreach ($toForgetClasses as $class) {
      if (array_key_exists($class, $identityMap)) {
        foreach ($identityMap[$class] as $entity) {
          $this->em->detach($entity);
        }
      }
    }
    if (array_key_exists($tournamentHierarchyEntity, $identityMap)) {
      foreach ($identityMap[$tournamentHierarchyEntity] as $entity) {
        if (!$entity instanceof TournamentInterface && !$entity instanceof CompetitionInterface) {
          $this->em->detach($entity);
        }
      }
    }
  }

  /**
   * @param TournamentHierarchyEntity $entity
   * @param array $rankingSystems
   */
  private function initInfluencingRankingSystemCompleteRecursiveDown(TournamentHierarchyEntity $entity,
                                                                     array $rankingSystems)
  {
    $rankingSystems = array_merge($rankingSystems, $entity->getRankingSystems()->toArray());
    foreach ($rankingSystems as $rankingSystem) {
      $this->addInfluencingRankingSystem($entity, $rankingSystem);
    }
    foreach ($entity->getChildren() as $child) {
      $this->initInfluencingRankingSystemCompleteRecursiveDown($child, $rankingSystems);
    }
  }

  /**
   * @param TournamentHierarchyEntity $entity
   */
  private function initInfluencingRankingSystemsComplete(TournamentHierarchyEntity $entity)
  {
    $rankingSystems = [];
    $tmp = $entity;
    while ($tmp->getParent() !== null) {
      $tmp = $tmp->getParent();
      $rankingSystems = array_merge($rankingSystems, $tmp->getRankingSystems()->toArray());
    }
    $this->initInfluencingRankingSystemCompleteRecursiveDown($entity, $rankingSystems);
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
    $this->em->remove($competition);
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

  /*private function adaptEarliestInfluence(array &$earliestInfluence, $newInfluence)
  {
    foreach ($newInfluence as $key => $influence) {
      if (!array_key_exists($key, $earliestInfluence) ||
        $influence['earliestInfluence'] < $earliestInfluence[$key]['earliestInfluence']) {
        $earliestInfluence[$key] = $influence;
      }
    }
  }

  private function adaptEarliestInfluenceByCompetition(RankingSystemServiceInterface $rss, Competition $competition,
                                                       array &$earliestInfluence) {
    $this->adaptEarliestInfluence($earliestInfluence, $rss->getRankingSystemsEarliestInfluences($competition));
  }*/

  /**
   * Removes the given membership from the database
   * @param TeamMembership $membership
   */
  private function removeMembership(TeamMembership $membership)
  {
    $membership->getTeam()->getMemberships()->removeElement($membership);
    $this->em->remove($membership);
  }

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

    foreach ($phase->getPreQualifications()->toArray() as $system) {
      /** @var $system QualificationSystem */
      $phase->getPreQualifications()->removeElement($system);
      // all systems in the array have a next phase and the phase has a phase number
      $system->getPreviousPhase()->getPostQualifications()->removeElement($system);
      $this->em->remove($system);
    }

    foreach ($phase->getPostQualifications()->toArray() as $system) {
      /** @var $system QualificationSystem */
      $phase->getPostQualifications()->removeElement($system);
      // all systems in the array have a next phase and the phase has a phase number
      $system->getNextPhase()->getPreQualifications()->removeElement($system);
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
   * Removes the given team from the database
   * @param Team $team
   */
  private function removeTeam(Team $team)
  {
    foreach ($team->getMemberships() as $membership) {
      $this->removeMembership($membership);
    }
    $this->em->remove($team);
  }

  /**
   * Replaces the competitions of the given tournament according to the request
   * @param mixed $input request input
   * @param $reportProgress
   * @param Tournament $tournament the tournament to modify
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
  private function replaceCompetitions($input, Tournament $tournament, $reportProgress)
  {
    $competitionNames = [];
    $competitionsByName = [];

    /** @var Competition[] $competitions */
    $competitions = $this->em->getRepository(Competition::class)->findBy(
      ['tournament' => $tournament]
    );

    foreach ($competitions as $competition) {
      $competitionNames[$competition->getName()] = false;
      $competitionsByName[$competition->getName()] = $competition;
    }
    unset($competitions);
    $numCompetitions = count($input['competitions']);
    $count = 0;
    foreach ($input['competitions'] as $competitionValues) {
      $competition = null;
      if (array_key_exists($competitionValues['name'], $competitionNames)) {
        // competition names are ensured to be unique by the request validation
        assert($competitionNames[$competitionValues['name']] === false);
        $competition = $competitionsByName[$competitionValues['name']];
        $this->ls->loadEntities([$competition]);
        $this->initInfluencingRankingSystemsComplete($competition);

        //$this->adaptEarliestInfluenceByCompetition($rss, $competition, $earliestInfluence);
        Tools::setFromSpecification($competition, $this->competitionSpecification, $competitionValues, $this->em);
      } else {
        $competition = new Competition();
        Tools::setFromSpecification($competition, $this->competitionSpecification, $competitionValues, $this->em);
        $competition->setTournamentWithoutInitializing($tournament);
        $this->em->persist($competition);
        foreach ($this->rankingSystems[Level::COMPETITION] as $rankingSystem) {
          $competition->getRankingSystems()->set($rankingSystem->getId(), $rankingSystem);
          $this->addInfluencingRankingSystem($competition, $rankingSystem);
          //$rankingSystem->getHierarchyEntries()->set($competition->getId(), $competition);
        }
      }
      $competitionNames[$competitionValues['name']] = true;


      // request
      $this->replaceTeams($competition, $competitionValues['teams']);
      // request
      $this->replacePhases($competition, $competitionValues['phases']);
      if (array_key_exists($competitionValues['name'], $competitionsByName)) {
        unset($competitionsByName[$competitionValues['name']]);
      }
      //$this->adaptEarliestInfluenceByCompetition($rss, $competition, $earliestInfluence);
      $count++;
      $reportProgress($count / ($numCompetitions + 1));
      $this->flushAndForgetEntities();
      $this->em->detach($competition);
    }
    foreach ($competitionNames as $key => $used) {
      if (!$used) {
        $competition = $competitionsByName[$key];
        $this->ls->loadEntities([$competition]);
        $this->initInfluencingRankingSystemsComplete($competition);
        //$this->adaptEarliestInfluenceByCompetition($rss, $competition, $earliestInfluence);
        $this->removeCompetition($competition);
        $this->flushAndForgetEntities();
        unset($competitionsByName[$key]);
      }
    }
  }

  /**
   * Replaces the game players of the given game according to the request
   * @param Game $game the game to modify
   * @param int[] $playerIds the list of the player ids
   * @param string $teamLetter the team letter to modify (A or B)
   * @param bool[] $otherPlayerIds the player ids of the other team already parsed
   * @throws DuplicateException Either a duplicate player id in this list or a player id in the list which is
   *                            also in $other_player_ids.
   *                                     phase or its phase has no phase number or no competition or its competition has
   *                                     no name
   * @throws ReferenceException a player for this team is not in the players lists of this team
   */
  private function replaceGamePlayers(Game $game, array $playerIds, string $teamLetter, array &$otherPlayerIds)
  {
    $internalPlayerIds = [];
    $method = 'getPlayers' . $teamLetter;

    /** @var Collection|Player[] $players */
    $players = $game->$method();
    foreach ($players->getKeys() as $id) {
      $internalPlayerIds[$id] = false;
    }

    $match = $game->getMatch();
    $phase = $match->getPhase();
    foreach ($playerIds as $id) {
      if (array_key_exists($id, $internalPlayerIds) && $internalPlayerIds[$id] == true) {
        //duplicate player!
        throw new DuplicateException($id, 'player id',
          'the players ' . $teamLetter . ' list of the game with game number ' . $game->getGameNumber() .
          ' of the match with match number ' . $match->getMatchNumber() . ' of the phase ' . $phase->getPhaseNumber() .
          ' of the competition ' . $phase->getCompetition()->getName());
      } else if (array_key_exists($id, $otherPlayerIds)) {
        //duplicate player!
        throw new DuplicateException($id, 'player id',
          'the players A and players B lists of the game with game number ' . $game->getGameNumber() .
          ' of the match with match number ' . $match->getMatchNumber() . ' of the phase ' . $phase->getPhaseNumber() .
          ' of the competition ' . $phase->getCompetition()->getName());
      }
      if (!array_key_exists($id, $internalPlayerIds)) {
        /** @var Player $player */
        $player = $this->em->find(Player::class, $id);
        $player = $player->getPlayer();
        $id = $player->getId();
        if (array_key_exists($id, $internalPlayerIds) && $internalPlayerIds[$id] == true) {
          //duplicate player!
          throw new DuplicateException($id, 'player id',
            'the players ' . $teamLetter . ' list of the game with game number ' . $game->getGameNumber() .
            ' of the match with match number ' . $match->getMatchNumber() . ' of the phase ' .
            $phase->getPhaseNumber() . ' of the competition ' . $phase->getCompetition()->getName());
        } else if (array_key_exists($id, $otherPlayerIds)) {
          //duplicate player!
          throw new DuplicateException($id, 'player id',
            'the players A and players B lists of the game with game number ' . $game->getGameNumber() .
            ' of the match with match number ' . $match->getMatchNumber() . ' of the phase ' .
            $phase->getPhaseNumber() . ' of the competition ' . $phase->getCompetition()->getName());
        }
        if (!array_key_exists($id, $internalPlayerIds)) {
          $found = false;
          $rankingsMethod = 'getRankings' . $teamLetter;
          /** @var Ranking[] $rankings */
          $rankings = $game->getMatch()->$rankingsMethod();
          foreach ($rankings as $ranking) {
            foreach ($ranking->getTeams() as $team) {
              if ($team->getMemberships()->exists(
                function (/** @noinspection PhpUnusedParameterInspection */
                  $_, TeamMembershipInterface $m) use ($id) {
                  return $m->getPlayer()->getId() == $id;
                }
              )) {
                $found = true;
                break;
              }
            }
            if ($found) {
              break;
            }
          }
          if (!$found) {
            throw new ReferenceException($id, "player id in players " . $teamLetter . " in game " .
              $game->getGameNumber() . " of match " . $match->getMatchNumber() . " of phase "
              . $phase->getPhaseNumber() . " of competition " . $phase->getCompetition()->getName() .
              ", which is not in the players lists of the teams of team " . $teamLetter);
          }
          $players->set($id, $player);
        }
      }
      $otherPlayerIds[$id] = true;
      $internalPlayerIds[$id] = true;
    }
    foreach ($internalPlayerIds as $id => $used) {
      if (!$used) {
        $players->remove($id);
      }
    }
  }

  /**
   * Replaces the games of the given match according to the request
   * @param Match $match the match to modify
   * @param mixed[] $values the request values for the games
   * @throws DuplicateException A game number is occurring twice or a player id is specified twice for the players of
   *                            this game
   *                                     no competition or its competition has no name
   * @throws ReferenceException a player of a team is not in the players lists of this team
   */
  private function replaceGames(Match $match, array $values)
  {
    $gameNumbers = [];
    foreach ($match->getGames()->getKeys() as $key) {
      $gameNumbers[$key] = false;
    }
    foreach ($values as $gameValues) {
      if (array_key_exists($gameValues['gameNumber'], $gameNumbers)) {
        if ($gameNumbers[$gameValues['gameNumber']] == true) {
          //duplicate match number!
          $phase = $match->getPhase();
          throw new DuplicateException($gameValues['gameNumber'], 'game number',
            'the game list of match ' . $match->getMatchNumber() . ' of phase ' . $phase->getPhaseNumber() .
            ' of the competition ' . $phase->getCompetition()->getName());
        }
        $game = $match->getGames()->get($gameValues['gameNumber']);
        Tools::setFromSpecification($game, $this->gameSpecification, $gameValues, $this->em);
      } else {
        $game = new Game();
        Tools::setFromSpecification($game, $this->gameSpecification, $gameValues, $this->em);
        // request
        $game->setMatch($match);
        $this->em->persist($game);
        foreach ($this->rankingSystems[Level::GAME] as $rankingSystem) {
          $game->getRankingSystems()->set($rankingSystem->getId(), $rankingSystem);
          $this->addInfluencingRankingSystem($game, $rankingSystem);
          //$rankingSystem->getHierarchyEntries()->set($game->getId(), $game);
        }
      }
      $gameNumbers[$gameValues['gameNumber']] = true;

      //the game has a match and a game number since it is required for the request
      $playerIds = [];
      $this->replaceGamePlayers($game, $gameValues['playersA'], 'A', $playerIds);
      $this->replaceGamePlayers($game, $gameValues['playersB'], 'B', $playerIds);
    }
    foreach ($gameNumbers as $key => $used) {
      if (!$used) {
        $game = $match->getGames()->get($key);
        $match->getGames()->remove($key);
        $this->removeGame($game);
      }
    }
  }


  /**
   * Replaces the match rankings of the given match according to the request
   * @param Match $match the match to modify
   * @param int[] $rankingValues the list of the unique rankings
   * @param string $teamLetter the team letter to modify (A or B)
   * @param bool[] $uRanks the ranks of the other team already parsed
   * @throws DuplicateException Either a duplicate unique rank in this list or a unique rank in the list which is
   *                            also in u_ranks.
   * @throws ReferenceException A unique rank is in the list which is not a valid unique rank for this phase
   *                                      or no competition or its competition has no name
   */
  private function replaceMatchRankings(Match $match, array $rankingValues, string $teamLetter, array &$uRanks)
  {
    $uniqueRanks = [];
    $method = 'getRankings' . $teamLetter;

    /** @var Collection|Ranking[] $rankings */
    $rankings = $match->$method();
    foreach ($rankings->getKeys() as $uniqueRank) {
      $uniqueRanks[$uniqueRank] = false;
    }

    $phase = $match->getPhase();
    foreach ($rankingValues as $uniqueRank) {
      if (array_key_exists($uniqueRank, $uniqueRanks)) {
        if ($uniqueRanks[$uniqueRank] == true) {
          //duplicate rank!
          throw new DuplicateException($uniqueRank, 'unique rank',
            'the rankings ' . $teamLetter . ' list of the match with match number ' .
            $match->getMatchNumber() . ' of the phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
      } else if (array_key_exists($uniqueRank, $uRanks)) {
        //duplicate rank!
        throw new DuplicateException($uniqueRank, 'unique rank',
          'the rankings A and ranking B lists of the match with match number ' . $match->getMatchNumber() .
          ' of the phase ' . $phase->getPhaseNumber() . ' of the competition ' . $phase->getCompetition()->getName());
      } else {
        if (!$phase->getRankings()->containsKey($uniqueRank)) {
          throw new ReferenceException($uniqueRank, 'unique rank in phase ' . $phase->getPhaseNumber() .
            ' of the competition ' . $phase->getCompetition()->getName());
        }
        $rankings->set($uniqueRank, $phase->getRankings()[$uniqueRank]);
      }

      $uRanks[$uniqueRank] = true;

      $uniqueRanks[$uniqueRank] = true;
    }
    foreach ($uniqueRanks as $uniqueRank => $used) {
      if (!$used) {
        $rankings->remove($uniqueRank);
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
   * @throws ReferenceException A unique rank is in one of the rankings lists which is not a valid unique rank for this
   *                            phase or a player of a team is not in the players lists of this team
   */
  private function replaceMatches(Phase $phase, array $values)
  {
    $matchNumbers = [];
    foreach ($phase->getMatches()->getKeys() as $key) {
      $matchNumbers[$key] = false;
    }
    foreach ($values as $matchValues) {
      if (array_key_exists($matchValues['matchNumber'], $matchNumbers)) {
        if ($matchNumbers[$matchValues['matchNumber']] == true) {
          //duplicate match number!
          throw new DuplicateException($matchValues['matchNumber'], 'match number',
            'the match list of phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
        $match = $phase->getMatches()->get($matchValues['matchNumber']);
        Tools::setFromSpecification($match, $this->matchSpecification, $matchValues, $this->em);
      } else {
        $match = new Match();
        Tools::setFromSpecification($match, $this->matchSpecification, $matchValues, $this->em);
        // request
        $match->setPhase($phase);
        $this->em->persist($match);
        foreach ($this->rankingSystems[Level::MATCH] as $rankingSystem) {
          $match->getRankingSystems()->set($rankingSystem->getId(), $rankingSystem);
          $this->addInfluencingRankingSystem($match, $rankingSystem);
          //$rankingSystem->getHierarchyEntries()->set($match->getId(), $match);
        }
      }
      $matchNumbers[$matchValues['matchNumber']] = true;

      //the ranking has a phase and a unique rank since it is required for the request
      $uniqueRanks = [];
      $this->replaceMatchRankings($match, $matchValues['rankingsAUniqueRanks'], 'A', $uniqueRanks);
      $this->replaceMatchRankings($match, $matchValues['rankingsBUniqueRanks'], 'B', $uniqueRanks);
      // the match has a match number and a phase since it is required for the request
      $this->replaceGames($match, $matchValues['games']);
    }
    foreach ($matchNumbers as $key => $used) {
      if (!$used) {
        $match = $phase->getMatches()->get($key);
        $phase->getMatches()->remove($key);
        $this->removeMatch($match);
      }
    }
  }

  /**
   * Replaces the next phases of the given phase according to the request.
   * It also replaces the corresponding previous phases of the next phases.
   * @param Phase $phase the phase to modify
   * @param int[] $otherPhaseNumbers list of next phase numbers
   * @throws DuplicateException a phase number was specified twice
   *                                     no name
   * @throws ReferenceException a referenced phase number in the next phases array does not exist
   * @throws UnorderedPhaseNumberException
   */
  private function replacePhaseNextPhases(Phase $phase, array $otherPhaseNumbers)
  {
    $nextPhaseNumbers = [];
    foreach ($phase->getPostQualifications() as $system) {
      // all systems in the array have a next phase and the phase has a phase number
      $nextPhaseNumbers[$system->getNextPhase()->getPhaseNumber()] = false;
    }
    foreach ($otherPhaseNumbers as $phaseNumber) {
      if ($phaseNumber <= $phase->getPhaseNumber()) {
        throw new UnorderedPhaseNumberException($phase->getPhaseNumber(), $phaseNumber);
      }
      if (array_key_exists($phaseNumber, $nextPhaseNumbers)) {
        if ($nextPhaseNumbers[$phaseNumber] == true) {
          //duplicate next phase number!
          throw new DuplicateException($phaseNumber, 'next phase number',
            'the next phase numbers list of phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
      } else {
        if (!$phase->getCompetition()->getPhases()->containsKey($phaseNumber)) {
          throw new ReferenceException($phaseNumber, "phase numbers in competition " .
            $phase->getCompetition()->getName());
        }
        $system = new QualificationSystem();
        $nextPhase = $phase->getCompetition()->getPhases()[$phaseNumber];
        $system->setNextPhase($nextPhase);
        $system->setPreviousPhase($phase);
        $this->em->persist($system);
      }
      $nextPhaseNumbers[$phaseNumber] = true;
    }
    foreach ($phase->getPostQualifications()->toArray() as $system) {
      /** @var $system QualificationSystem */
      // all systems in the array have a next phase and the phase has a phase number
      if ($nextPhaseNumbers[$system->getNextPhase()->getPhaseNumber()] === false) {
        $phase->getPostQualifications()->removeElement($system);
        // all systems in the array have a next phase and the phase has a phase number
        $system->getNextPhase()->getPreQualifications()->removeElement($system);
        $this->em->remove($system);
      }
    }
  }

  /**
   * Replaces the phases of the given competition according to the request
   * @param Competition $competition the competition to modify
   * @param mixed[] $values the request values for the phases
   * @throws DuplicateException two phases of a competition have the same phase number or a unique rank is occurring
   *                            twice or a team start number is specified twice for a ranking or a match number is
   *                            occurring twice or a unique rank is specified twice for the two teams in a match or a
   *                            game number is occurring twice or a player id is specified twice for the players of a
   *                            game
   * @throws ReferenceException a referenced phase number in the next phases array does not exist or a referenced team
   *                            start number in the team values does not exist or a referenced unique rank in the
   *                            rankings lists of a match does not exist in the rankings of the phase or a player of a
   *                            team is not in the players lists of this team
   * @throws UnorderedPhaseNumberException
   */
  private function replacePhases(Competition $competition, array $values)
  {
    $phaseNumbers = [];
    foreach ($competition->getPhases() as $phase) {
      $phaseNumbers[$phase->getPhaseNumber()] = false;
    }
    foreach ($values as $phaseValues) {
      $phase = null;
      if (array_key_exists($phaseValues['phaseNumber'], $phaseNumbers)) {
        if ($phaseNumbers[$phaseValues['phaseNumber']] == true) {
          //duplicate phase number
          throw new DuplicateException($phaseValues['phaseNumber'], 'phase number',
            'the phase list of the competition ' . $competition->getName());
        }
        $phase = $competition->getPhases()->get($phaseValues['phaseNumber']);
        Tools::setFromSpecification($phase, $this->phaseSpecification, $phaseValues, $this->em);
      } else {
        $phase = new Phase();
        Tools::setFromSpecification($phase, $this->phaseSpecification, $phaseValues, $this->em);
        // request
        $phase->setCompetition($competition);
        $this->em->persist($phase);
        foreach ($this->rankingSystems[Level::PHASE] as $rankingSystem) {
          $phase->getRankingSystems()->set($rankingSystem->getId(), $rankingSystem);
          $this->addInfluencingRankingSystem($phase, $rankingSystem);
          //$rankingSystem->getHierarchyEntries()->set($phase->getId(), $phase);
        }
      }
      $phaseNumbers[$phaseValues['phaseNumber']] = true;
      $this->replaceRankings($phase, $phaseValues['rankings']);
      $this->replaceMatches($phase, $phaseValues['matches']);
    }

    foreach ($phaseNumbers as $key => $used) {
      if (!$used) {
        $phase = $competition->getPhases()[$key];
        $competition->getPhases()->remove($key);
        $this->removePhase($phase);
      }
    }
    foreach ($values as $phaseValues) {
      //replace next phases
      $phase = $competition->getPhases()->get($phaseValues['phaseNumber']);
      $this->replacePhaseNextPhases($phase, array_key_exists('nextPhaseNumbers', $phaseValues) ?
        $phaseValues['nextPhaseNumbers'] : []);
    }
  }

  /**
   * Replaces the teams of the given ranking according to the request
   * @param Ranking $ranking the ranking to modify
   * @param array $teamValues the list of the teams start numbers
   * @param string[] $otherStartNumbers team start numbers of other rankings
   * @throws DuplicateException a team start number is specified twice for this ranking
   *                                     competition or its competition has no name
   * @throws ReferenceException a referenced team start number in the team values does not exist
   */
  private function replaceRankingTeams(Ranking $ranking, array $teamValues, array &$otherStartNumbers)
  {
    $teamStartNumbers = [];
    foreach ($ranking->getTeams()->getKeys() as $startNumber) {
      $teamStartNumbers[$startNumber] = false;
    }
    foreach ($teamValues as $startNumber) {
      $phase = $ranking->getPhase();
      if (array_key_exists($startNumber, $teamStartNumbers) && $teamStartNumbers[$startNumber] == true) {
        //duplicate team!
        throw new DuplicateException($startNumber, 'team start number',
          'the team list of ranking ' . $ranking->getName() . '(Unique rank ' .
          $ranking->getUniqueRank() . ') of the phase ' . $phase->getPhaseNumber() . ' of the competition ' .
          $phase->getCompetition()->getName());
      } else if (array_key_exists($startNumber, $otherStartNumbers)) {
        throw new DuplicateException($startNumber, 'team start number',
          'the team start number lists of the ranking of the phase ' . $phase->getPhaseNumber() .
          ' of the competition ' . $phase->getCompetition()->getName());
      }
      if (!array_key_exists($startNumber, $teamStartNumbers)) {
        if (!$phase->getCompetition()->getTeams()->containsKey($startNumber)) {
          throw new ReferenceException($startNumber, "team start number in competition " .
            $phase->getCompetition()->getName());
        }
        $ranking->getTeams()->set($startNumber, $phase->getCompetition()->getTeams()->get($startNumber));
      }
      $otherStartNumbers[$startNumber] = true;
      $teamStartNumbers[$startNumber] = true;
    }
    foreach ($teamStartNumbers as $startNumber => $used) {
      if (!$used) {
        $ranking->getTeams()->remove($startNumber);
      }
    }
  }

  /**
   * Replaces the rankings of the given phase according to the request
   * @param Phase $phase the phase to modify
   * @param mixed[] $values the request values for the rankings
   * @throws DuplicateException a unique rank is occurring twice or a team start number is specified twice for this
   *                            ranking
   * @throws ReferenceException a referenced team start number in the team values does not exist
   */
  private function replaceRankings(Phase $phase, array $values)
  {
    $uniqueRanks = [];
    foreach ($phase->getRankings()->getKeys() as $key) {
      $uniqueRanks[$key] = false;
    }
    $teamStartNumbers = [];
    foreach ($values as $rankingValues) {
      if (array_key_exists($rankingValues['uniqueRank'], $uniqueRanks)) {
        if ($uniqueRanks[$rankingValues['uniqueRank']] == true) {
          //duplicate unique rank!
          throw new DuplicateException($rankingValues['uniqueRank'], 'unique rank',
            'the ranking list of phase ' . $phase->getPhaseNumber() . ' of the competition ' .
            $phase->getCompetition()->getName());
        }
        $ranking = $phase->getRankings()->get($rankingValues['uniqueRank']);
        Tools::setFromSpecification($ranking, $this->rankingSpecification, $rankingValues, $this->em);
      } else {
        $ranking = new Ranking();
        Tools::setFromSpecification($ranking, $this->rankingSpecification, $rankingValues, $this->em);
        // request
        $ranking->setPhase($phase);
        $this->em->persist($ranking);
      }
      $uniqueRanks[$rankingValues['uniqueRank']] = true;
      //the ranking has a phase and a unique rank since it is required for the request
      $this->replaceRankingTeams($ranking, $rankingValues['teamStartNumbers'], $teamStartNumbers);
    }
    foreach ($uniqueRanks as $key => $used) {
      if (!$used) {
        $ranking = $phase->getRankings()->get($key);
        $phase->getRankings()->remove($key);
        $this->removeRanking($ranking);
      }
    }
  }

  /**
   * Replaces the players of the given team according to the request
   * @param Team $team the team to modify
   * @param mixed[] $playerValues the request values for the players
   * @throws DuplicateException a player is specified twice for this team
   */
  private function replaceTeamPlayers(Team $team, array $playerValues)
  {
    $playerIds = [];
    foreach ($team->getMemberships() as $m) {
      $playerIds[$m->getPlayer()->getId()] = $m;
    }
    foreach ($playerValues as $playerId) {
      if (array_key_exists($playerId, $playerIds)) {
        if ($playerIds[$playerId] === null) {
          //duplicate player!
          throw new DuplicateException($playerId, 'player id',
            'the player list of team ' . $team->getName());
        }
      } else {
        /** @var Player $player */
        $player = $this->em->find(Player::class, $playerId);
        $player = $player->getPlayer();
        $playerId = $player->getId();
        if (array_key_exists($playerId, $playerIds)) {
          if ($playerIds[$playerId] === null) {
            //duplicate player!
            throw new DuplicateException($playerId, 'player id',
              'the player list of team ' . $team->getName());
          }
        } else {
          $membership = new TeamMembership();
          $membership->setPlayer($player);
          $this->em->persist($membership);
          $membership->setTeam($team);
        }
      }
      $playerIds[$playerId] = null;
    }
    foreach ($playerIds as $id => $m) {
      if ($m !== null) {
        $this->removeMembership($m);
      }
    }
  }

  /**
   * Replaces the teams of the given competition according to the request
   * @param Competition $competition the competition to modify
   * @param mixed[] $values the request values for the teams
   * @throws DuplicateException a team start number is occurring twice or a player is specified twice for a team
   */
  private function replaceTeams(Competition $competition, array $values)
  {
    $oldStartNumbers = [];
    foreach ($competition->getTeams() as $team) {
      $oldStartNumbers[$team->getStartNumber()] = false;
    }
    foreach ($values as $teamValues) {
      if (array_key_exists($teamValues['startNumber'], $oldStartNumbers)) {
        if ($oldStartNumbers[$teamValues['startNumber']] == true) {
          //duplicate start number!
          throw new DuplicateException($teamValues['startNumber'], 'start number',
            'the team list of competition ' . $competition->getName());
        }
        $team = $competition->getTeams()->get($teamValues['startNumber']);
        Tools::setFromSpecification($team, $this->teamSpecification, $teamValues, $this->em);
      } else {
        $team = new Team();
        Tools::setFromSpecification($team, $this->teamSpecification, $teamValues, $this->em);
        // request
        $team->setCompetition($competition);
        $this->em->persist($team);
      }
      $oldStartNumbers[$teamValues['startNumber']] = true;
      $this->replaceTeamPlayers($team, $teamValues['players']);
    }
    foreach ($oldStartNumbers as $key => $used) {
      if (!$used) {
        $team = $competition->getTeams()->get($key);
        $competition->getTeams()->remove($key);
        $this->removeTeam($team);
      }
    }
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
      $prefix . 'startTime' => ['validation' => 'date_format:' . Tools::getDatetimetzFormat(),
        'transformer' => Tools::datetimetzTransformer(), 'default' => null],
      $prefix . 'endTime' => ['validation' => 'date_format:' . Tools::getDatetimetzFormat(),
        'transformer' => Tools::datetimetzTransformer(), 'default' => null]
    ];
  }
//</editor-fold desc="Private Methods">
}