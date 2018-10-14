<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Service\AsyncServices\CreateOrReplaceTournamentInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class TournamentController
 * @package App\Http\Controllers
 */
class TournamentController extends AsyncableController
{
  /**
   * creates or replaces an existing tournament
   *
   * @param Request $request the http request
   * @return JsonResponse
   */
  public function createOrReplaceTournament(Request $request): JsonResponse
  {
    return $this->checkAsync($request, CreateOrReplaceTournamentInterface::class);
  }
//</editor-fold desc="Public Methods">
}
