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

    $existingPlayers = [];
    $input = $request->input();
    $players = [];
    $inputPlayerData = [];
    foreach ($input as $player) {
      //ignore duplicate players
      if (!array_key_exists($player['firstName'], $inputPlayerData)) {
        $inputPlayerData[$player['firstName']] = [];
      }
      if (!array_key_exists($player['lastName'], $inputPlayerData[$player['firstName']])) {
        $inputPlayerData[$player['firstName']][$player['lastName']] = [];
      }
      if (!array_key_exists($player['birthday'], $inputPlayerData[$player['firstName']][$player['lastName']])) {
        $inputPlayerData[$player['firstName']][$player['lastName']][$player['birthday']] = true;
        $player['birthday'] = new \DateTime($player['birthday']);
        //check if player already exists
        $result = $this->entityManager->getRepository(Player::class)->findBy($player);
        if (count($result) > 0) {
          $existingPlayers[] = $result[0];
        } else {
          $p = $this->setFromSpecification(new Player(), $specification, $player);
          $this->entityManager->persist($p);
          $players[] = $p;
        }
      }
    }
    if (count($existingPlayers) > 0) {
      throw new PlayerAlreadyExists($existingPlayers);
    }
    $this->entityManager->flush();

    return response()->json(array_map(function (Player $p) {
      return ["firstName" => $p->getFirstName(), "lastName" => $p->getLastName(), "id" => $p->getPlayerId(),
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
      $result = $this->entityManager->getRepository(Player::class)->findBy($criteria);
      $found = [];
      foreach ($result as $p) {
        /** @noinspection PhpUnhandledExceptionInspection */ //all values must be set since we used them in the
        // criteria (findBy)
        $found[] = ['id' => $p->getPlayerId(), 'firstName' => $p->getFirstName(), 'lastName' => $p->getLastName(),
          'birthday' => $p->getBirthday()->format('Y-m-d')];
      }
      $results[] = ['search' => $player, 'found' => $found];
    }

    return response()->json($results);
  }
//</editor-fold desc="Public Methods">
}