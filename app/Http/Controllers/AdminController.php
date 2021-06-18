<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/17/18
 * Time: 12:00 PM
 */

namespace App\Http\Controllers;


use App\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\Service\PlayerServiceInterface;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;

class AdminController extends BaseController
{
//<editor-fold desc="Public Methods">
  public function mergePlayers(Request $request, EntityManagerInterface $em, PlayerServiceInterface $ps,
                               RankingSystemServiceInterface $rs): JsonResponse
  {
    $this->validate($request, [
      'player1' => 'required|exists:App\Entity\Player,id',
      'player2' => 'required|different:player1|exists:App\Entity\Player,id'
    ]);

    /** @var Player $player1 */
    $player1 = $this->getEntityManager()->find(Player::class, $request->input('player1'));
    $player1 = $player1->getPlayer();

    /** @var Player $player2 */
    $player2 = $this->getEntityManager()->find(Player::class, $request->input('player2'));

    $result = $ps->mergePlayers($player1, $player2);

    if ($result !== true) {
      return response()->json($result);
    }

    // we need to reload since mergePlayers might clear the entity manager
    $player2 = $this->getEntityManager()->find(Player::class, $request->input('player2'));
    $player1 = $this->getEntityManager()->find(Player::class, $request->input('player1'));
    $player1 = $player1->getPlayer();

    $player2->setMergedInto($player1);
    if ($player2->getItsfLicenseNumber() !== null && $player1->getItsfLicenseNumber() === null) {
      $player2->setItsfLicenseNumber($player1->getItsfLicenseNumber());
    }

    //flatten merged players relation
    foreach ($player2->getMergedPlayers()->toArray() as $subPlayer) {
      /** @var Player $subPlayer */
      $subPlayer->setMergedInto($player1);
    }

    $em->flush();

    return response()->json($result);
  }
//</editor-fold desc="Public Methods">
}