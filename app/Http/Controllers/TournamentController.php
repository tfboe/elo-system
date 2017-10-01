<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Entity\Categories\GameMode;
use App\Entity\Categories\OrganizingMode;
use App\Entity\Categories\ScoreMode;
use App\Entity\Categories\Table;
use App\Entity\Categories\TeamMode;
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
    $specification = [
      'userIdentifier' => ['validation' => 'required|string'],
      'name' => ['validation' => 'required|string'],
      'tournamentListId' => ['validation' => 'string'],
      'gameMode' => ['validation' => 'string|in:' . implode(",", GameMode::getNames()),
        'transformer' => $this->enumTransformer(GameMode::class)],
      'organizingMode' => ['validation' => 'string|in:' . implode(",", OrganizingMode::getNames()),
        'transformer' => $this->enumTransformer(OrganizingMode::class)],
      'scoreMode' => ['validation' => 'string|in:' . implode(",", ScoreMode::getNames()),
        'transformer' => $this->enumTransformer(ScoreMode::class)],
      'table' => ['validation' => 'string|in:' . implode(",", Table::getNames()),
        'transformer' => $this->enumTransformer(Table::class)],
      'teamMode' => ['validation' => 'string|in:' . implode(",", TeamMode::getNames()),
        'transformer' => $this->enumTransformer(TeamMode::class)],
    ];

    $this->validateBySpecification($request, $specification);

    assert(\Auth::user()->getId() != null);
    /** @var Tournament $tournament */
    $tournament = $this->setFromSpecification(new Tournament(), $specification, $request->input());
    $tournament->setCreator(\Auth::user());

    $this->em->persist($tournament);
    $this->em->flush();

    return response()->json(['type' => 'create']);
  }
//</editor-fold desc="Public Methods">
}
