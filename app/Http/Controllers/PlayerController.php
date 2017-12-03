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
use App\Exceptions\PlayerAlreadyExists;
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
   * Adds new players to the database, should only be called after thoroughly checking if the players were already in
   * the database using the searchPlayers method.
   *
   * @param Request $request
   * @return JsonResponse
   * @throws PlayerAlreadyExists at least one of the given players is already in the database
   */
  public function addPlayers(Request $request): JsonResponse
  {
    $specification = [
      '*.firstName' => ['validation' => 'required|string|min:2'],
      '*.lastName' => ['validation' => 'required|string|min:2'],
      '*.birthday' => ['validation' => 'required|date'],
    ];

    $this->validateBySpecification($request, $specification);

    $already_existing_players = [];
    $input = $request->input();
    foreach ($input as &$player_ref) {
      $player_ref['birthday'] = new \DateTime($player_ref['birthday']);
    }
    usort($input, function ($x, $y) {
      $comp = $x['firstName'] <=> $y['firstName'];
      if ($comp !== 0) {
        return $comp;
      }
      $comp = $x['lastName'] <=> $y['lastName'];
      if ($comp !== 0) {
        return $comp;
      }
      return $x['birthday'] <=> $y['birthday'];
    });
    $last = null;
    $players = [];
    foreach ($input as $player) {
      if ($last == null || $player['firstName'] != $last['firstName'] || $player['lastName'] != $last['lastName'] ||
        $player['birthday'] != $last['birthday']) {
        $last = $player;
        $result = $this->em->getRepository(Player::class)->findBy($player);
        if (count($result) > 0) {
          $already_existing_players[] = $result[0];
        } else {
          $p = $this->setFromSpecification(new Player(), $specification, $player);
          $this->em->persist($p);
          $players[] = $p;
        }
      }
    }
    if (count($already_existing_players) > 0) {
      throw new PlayerAlreadyExists($already_existing_players);
    }
    $this->em->flush();

    return response()->json(array_map(function (Player $p) {
      return ["firstName" => $p->getFirstName(), "lastName" => $p->getLastName(), "id" => $p->getId(),
        "birthday" => $p->getBirthday()->format("Y-m-d")];
    }, $players));
  }

  /** @noinspection PhpDocMissingThrowsInspection */
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
        /** @noinspection PhpUnhandledExceptionInspection */ //all values must be set since we used them in the
        // criteria (findBy)
        $found[] = ['id' => $p->getId(), 'firstName' => $p->getFirstName(), 'lastName' => $p->getLastName(),
          'birthday' => $p->getBirthday()->format('Y-m-d')];
      }
      $results[] = ['search' => $player, 'found' => $found];
    }

    return response()->json($results);
  }
//</editor-fold desc="Public Methods">
}