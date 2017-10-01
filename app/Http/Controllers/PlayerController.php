<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 8:45 PM
 */

namespace App\Http\Controllers;


use App\Entity\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class PlayerController
 * @package App\Http\Controllers
 */
class PlayerController extends BaseController
{
//<editor-fold desc="Public Methods">
  /**
   * Searches for players by name and birthday and returns the found results in a json format.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function searchPlayers(Request $request): JsonResponse
  {
    $specification = [
      '*.firstName' => ['validation' => 'required|string|min:2'],
      '*.lastName' => ['validation' => 'required|string|min:2'],
      '*.birthday' => ['validation' => 'date'],
    ];

    $this->validateBySpecification($request, $specification);

    $results = [];
    foreach ($request->input() as $player) {
      $criteria = $player;
      if (array_key_exists('birthday', $criteria)) {
        $criteria['birthday'] = new \DateTime($criteria['birthday']);
      }
      /** @var Player[] $result */
      $result = $this->em->getRepository(Player::class)->findBy($criteria);
      $found = [];
      foreach ($result as $p) {
        $found[] = ['id' => $p->getId(), 'firstName' => $p->getFirstName(), 'lastName' => $p->getLastName(),
          'birthday' => $p->getBirthday()->format('Y-m-d')];
      }
      $results[] = ['search' => $player, 'found' => $found];
    }

    return response()->json($results);
  }
//</editor-fold desc="Public Methods">
}