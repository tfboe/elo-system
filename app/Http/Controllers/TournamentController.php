<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
use App\Entity\Competition;
use App\Entity\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TournamentController
 * @package App\Http\Controllers
 */
class TournamentController extends BaseController
{
//<editor-fold desc="Public Methods">
  /**
   * creates or updates an existing tournament
   *
   * @param Request $request the http request
   * @return JsonResponse
   */
  public function createOrUpdateTournament(Request $request): JsonResponse
  {
    $tournament_specification = [
      'userIdentifier' => ['validation' => 'required|string'],
      'name' => ['validation' => 'required|string'],
      'tournamentListId' => ['validation' => 'string'],
      'competitions' => ['validation' => 'required|array|min:1', 'ignore' => True]
    ];
    $tournament_specification = array_merge($tournament_specification, $this->categoriesSpecifications(''));

    $competition_specification = [
      'competitions.*.name' => ['validation' => 'required|string'],
    ];
    $competition_specification = array_merge($competition_specification,
      $this->categoriesSpecifications('competitions.*.'));

    $this->validateBySpecification($request, array_merge(
      $tournament_specification,
      $competition_specification));

    assert(\Auth::user()->getId() != null);
    /** @var Tournament|null $tournament */
    $tournament = $this->em->getRepository(Tournament::class)->findOneBy(
      ['userIdentifier' => $request->input('userIdentifier'), 'creator' => \Auth::user()]);
    $type = 'update';
    if ($tournament == null) {
      $tournament = new Tournament();
      $tournament->setCreator(\Auth::user());
      $this->em->persist($tournament);
      $type = 'create';
    }
    $this->setFromSpecification($tournament, $tournament_specification, $request->input());
    $old_keys_used = [];
    foreach ($tournament->getCompetitions() as $competition) {
      $old_keys_used[$competition->getName()] = false;
    }
    foreach ($request->input('competitions') as $competition_values) {
      $competition = null;
      if (array_key_exists($competition_values['name'], $old_keys_used)) {
        $competition = $tournament->getCompetitions()->get($competition_values['name']);
        $old_keys_used[$competition_values['name']] = true;
        $this->setFromSpecification($competition, $competition_specification, $competition_values);
      } else {
        $competition = new Competition();
        $this->setFromSpecification($competition, $competition_specification, $competition_values);
        $competition->setTournament($tournament);
        $this->em->persist($competition);
      }
    }
    foreach ($old_keys_used as $key => $used) {
      if (!$used) {
        $competition = $tournament->getCompetitions()->get($key);
        $tournament->getCompetitions()->remove($key);
        $this->em->remove($competition);
      }
    }
    $this->em->flush();

    return response()->json(['type' => $type]);
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
        'transformer' => $this->enumTransformer(GameMode::class)],
      $prefix . 'organizingMode' => ['validation' => 'string|in:' . implode(",", OrganizingMode::getNames()),
        'transformer' => $this->enumTransformer(OrganizingMode::class)],
      $prefix . 'scoreMode' => ['validation' => 'string|in:' . implode(",", ScoreMode::getNames()),
        'transformer' => $this->enumTransformer(ScoreMode::class)],
      $prefix . 'table' => ['validation' => 'string|in:' . implode(",", Table::getNames()),
        'transformer' => $this->enumTransformer(Table::class)],
      $prefix . 'teamMode' => ['validation' => 'string|in:' . implode(",", TeamMode::getNames()),
        'transformer' => $this->enumTransformer(TeamMode::class)],
    ];
  }
//</editor-fold desc="Private Methods">
}
