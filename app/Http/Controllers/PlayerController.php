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
use Tfboe\FmLib\Exceptions\DuplicateException;
use Tfboe\FmLib\Exceptions\PlayerAlreadyExists;
use Tfboe\FmLib\Http\Controllers\BaseController;


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
      '*.lastName' => ['validation' => 'required|string|min:1'],
      '*.birthday' => ['validation' => 'required|date'],
      '*.itsfLicenseNumber' => ['validation' => 'integer|min:1']
    ];

    $this->validateBySpecification($request, $specification);

    $existingPlayers = [];
    $input = $request->input();
    $players = [];
    /** @var Player[] $playerEntities */
    $playerEntities = [];
    $inputPlayerData = [];
    foreach ($input as $player) {
      if (!array_key_exists($player['firstName'], $inputPlayerData)) {
        $inputPlayerData[$player['firstName']] = [];
      }
      if (!array_key_exists($player['lastName'], $inputPlayerData[$player['firstName']])) {
        $inputPlayerData[$player['firstName']][$player['lastName']] = [];
      }
      if (!array_key_exists($player['birthday'], $inputPlayerData[$player['firstName']][$player['lastName']])) {
        $inputPlayerData[$player['firstName']][$player['lastName']][$player['birthday']] = null;
        $birthdayString = $player['birthday'];
        $player['birthday'] = new \DateTime($player['birthday']);
        $itsfLicenseNumber = null;
        if (array_key_exists('itsfLicenseNumber', $player)) {
          $itsfLicenseNumber = $player['itsfLicenseNumber'];
        }
        //check if player already exists
        $result = $this->getEntityManager()->getRepository(Player::class)
          ->findBy($this->extract($player, ['firstName', 'lastName', 'birthday']));
        if (count($result) > 0) {
          $existingPlayers[] = $result[0];
        } else {
          $alreadyExists = false;
          if ($itsfLicenseNumber !== null) {
            $result = $this->getEntityManager()->getRepository(Player::class)
              ->findBy(['itsfLicenseNumber' => $itsfLicenseNumber]);
            if (count($result) > 0) {
              $existingPlayers[] = $result[0];
              $alreadyExists = true;
            }
          }
          if (!$alreadyExists) {
            /** @var Player $p */
            $p = $this->setFromSpecification(new Player(), $specification, $player);
            $this->getEntityManager()->persist($p);
            $player['birthday'] = $birthdayString;
            $players[] = $player;
            $playerEntities[] = $p;
            $inputPlayerData[$player['firstName']][$player['lastName']][$player['birthday']] = $p;
          }
        }
      } else if ($inputPlayerData[$player['firstName']][$player['lastName']][$player['birthday']] != null) {
          $players[] = $player;
          $playerEntities[] = $inputPlayerData[$player['firstName']][$player['lastName']][$player['birthday']];
      }
    }
    if (count($existingPlayers) > 0) {
      throw new PlayerAlreadyExists($existingPlayers);
    }
    $this->getEntityManager()->flush();

    for ($i = 0; $i < count($playerEntities); $i++) {
      $players[$i]['id'] = $playerEntities[$i]->getId();
    }

    return response()->json($players);
  }

  /**
   * @param Request $request
   * @return JsonResponse
   * @throws DuplicateException
   * @throws PlayerAlreadyExists
   */
  public function updatePlayers(Request $request): JsonResponse
  {
    $specification = [
      '*.id' => ['validation' => 'required|exists:App\Entity\Player,id'],
      '*.firstName' => ['validation' => 'string|min:2'],
      '*.lastName' => ['validation' => 'string|min:1'],
      '*.birthday' => ['validation' => 'date'],
      '*.itsfLicenseNumber' => ['validation' => 'integer|min:1']
    ];

    $this->validateBySpecification($request, $specification);

    $input = $request->input();
    $alreadyExistingPlayers = [];
    $inputPlayerIds = [];
    foreach ($input as $player) {
      //throw error on duplicate updates
      if (array_key_exists($player['id'], $inputPlayerIds)) {
        throw new DuplicateException($player['id'], 'playerId', 'updatePlayers');
      }
      $inputPlayerData[$player['id']] = true;
      if (array_key_exists('birthday', $player)) {
        $player['birthday'] = new \DateTime($player['birthday']);
      }

      /** @var Player $p */
      $p = $this->getEntityManager()->find(Player::class, $player['id']);
      unset($player['id']);
      //check if no other player contains the same itsfLicenseNumber or firstName/lastName/birthday
      if (array_key_exists('itsfLicenseNumber', $player) &&
        $player['itsfLicenseNumber'] !== $p->getItsfLicenseNumber()) {
        $p2 = $this->getEntityManager()->getRepository(Player::class)
          ->findOneBy(['itsfLicenseNumber' => $player['itsfLicenseNumber']]);
        if ($p2 !== null) {
          $alreadyExistingPlayers[$p->getId()] = $p2;
        }
      }
      if ((array_key_exists('firstName', $player) && $player['firstName'] != $p->getFirstName()) ||
        (array_key_exists('lastName', $player) && $player['lastName'] != $p->getLastName()) ||
        (array_key_exists('birthday', $player) && $player['birthday'] != $p->getBirthday())
      ) {
        $p2 = $this->getEntityManager()->getRepository(Player::class)
          ->findOneBy(['firstName' => $player['firstName'], 'lastName' => $player['lastName'],
            'birthday' => $player['birthday']]);
        if ($p2 !== null && $p2->getId() !== $p->getId()) {
          $alreadyExistingPlayers[$p->getId()] = $p2;
        }
      }
      if (!array_key_exists($p->getId(), $alreadyExistingPlayers)) {
        $this->setFromSpecification($p, $specification, $player);
      }
    }

    if (count($alreadyExistingPlayers) > 0) {
      throw new PlayerAlreadyExists($alreadyExistingPlayers);
    }
    $this->getEntityManager()->flush();

    return response()->json(true);
  }

  /**
   * Searches for players by name and birthday and returns the found results in a json format.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function searchPlayers(Request $request): JsonResponse
  {
    $specification = [
      '*.firstName' => ['validation' => 'string|min:2'],
      '*.lastName' => ['validation' => 'string|min:1'],
      '*.birthday' => ['validation' => 'date'],
      '*.itsfLicenseNumber' => ['validation' => 'integer|min:1|required_without:*.firstName,*.lastName']
    ];

    $this->validateBySpecification($request, $specification);

    $parameters = [];
    $subQuery = "";
    $licenseNumbers = [];
    $keysOfLicenseNumber = [];
    $licenseNumbersQuery = "";
    $input = $request->input();
    foreach ($input as $key => $player) {
      assert($key !== null);
      if (array_key_exists('itsfLicenseNumber', $player)) {
        if (!array_key_exists($player['itsfLicenseNumber'], $keysOfLicenseNumber)) {
          $licenseNumbers[] = $player['itsfLicenseNumber'];
          $keysOfLicenseNumber[$player['itsfLicenseNumber']] = [$key];
          if ($licenseNumbersQuery !== "") {
            $licenseNumbersQuery .= ',';
          }
          $licenseNumbersQuery .= '?';
        } else {
          $keysOfLicenseNumber[$player['itsfLicenseNumber']][] = $key;
        }
      }
      if (array_key_exists('firstName', $player) && array_key_exists('lastName', $player) &&
        array_key_exists('birthday', $player)) {
        $birthday = new \DateTime($player['birthday']);
        if ($subQuery === "") {
          $subQuery .= "SELECT ? as k, ? as firstName, ? as lastName, ? as year";
        } else {
          $subQuery .= " UNION ALL SELECT ?,?,?,?";
        }
        $parameters[] = $key;
        $parameters[] = $player['firstName'];
        $parameters[] = $player['lastName'];
        $parameters[] = $birthday->format("Y");
      }
    }
    $metaData = $this->getEntityManager()->getClassMetadata(Player::class);
    $playerTable = $metaData->getTableName();
    $firstNameRow = $metaData->getColumnName("firstName");
    $lastNameRow = $metaData->getColumnName("lastName");
    $birthdayRow = $metaData->getColumnName("birthday");
    $itsfNumberRow = $metaData->getColumnName("itsfLicenseNumber");
    $mergedIntoId = $metaData->getAssociationMapping("mergedInto")['joinColumns'][0]['name'];
    $idRow = $metaData->getSingleIdentifierColumnName();
    $additionalSelect = "null as k";
    if ($subQuery != "") {
      $additionalSelect = "t.k as k";
    }
    $query = <<<SQL
SELECT IFNULL(p2.$idRow, p.$idRow) as id, IFNULL(p2.$firstNameRow, p.$firstNameRow) as firstName, 
       IFNULL(p2.$lastNameRow, p.$lastNameRow) as lastName, IFNULL(p2.$birthdayRow, p.$birthdayRow) as birthday, 
       IFNULL(p2.$itsfNumberRow, p.$itsfNumberRow) as itsfLicenseNumber, $additionalSelect
FROM $playerTable AS p
LEFT JOIN $playerTable AS p2
  ON p2.$idRow = p.$mergedIntoId 
SQL;
    if ($subQuery !== "") {
      $query .= <<<SQL
LEFT JOIN (
  $subQuery
) AS t
  ON t.firstName = p.$firstNameRow AND t.lastName = p.$lastNameRow AND t.year = YEAR(p.$birthdayRow)
WHERE t.k IS NOT NULL
SQL;
    }

    if ($licenseNumbersQuery != "") {
      if ($subQuery === "") {
        $query .= " WHERE";
      } else {
        $query .= " OR";
      }
      $query .= " p.$itsfNumberRow IN ($licenseNumbersQuery)";
    }
    $parameters = array_merge($parameters, $licenseNumbers);
    $query = $this->getEntityManager()->getConnection()->prepare($query);
    $query->execute($parameters);
    $rows = $query->fetchAll();

    $res = [];
    foreach ($rows as $row) {
      $f = array_intersect_key($row, array_flip(['id', 'firstName', 'lastName', 'birthday', 'itsfLicenseNumber']));
      $f['id'] = intval($f['id']);
      $keys = [];
      if ($row['k'] !== null) {
        $keys[] = $row['k'];
      }
      if ($row['itsfLicenseNumber'] !== null && array_key_exists($row['itsfLicenseNumber'], $keysOfLicenseNumber)) {
        $keys = array_merge($keys, $keysOfLicenseNumber[$row['itsfLicenseNumber']]);
      }
      assert(count($keys) > 0);
      foreach ($keys as $key) {
        if (!array_key_exists($key, $res)) {
          $res[$key] = [];
        }
        $res[$key][$row['id']] = $f;
      }
    }

    return response()->json($res);
  }

  /**
   * @param mixed[] $o
   * @param string[] $keys
   */
  private function extract(array $o, array $keys)
  {
    $res = [];
    foreach ($keys as $key) {
      $res[$key] = $o[$key];
    }
    return $res;
  }
//</editor-fold desc="Public Methods">
}