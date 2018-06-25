<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 8:45 PM
 */

namespace App\Http\Controllers;


use App\Entity\RankingSystemListEntry;
use Illuminate\Http\JsonResponse;
use Tfboe\FmLib\Http\Controllers\BaseController;
use Tfboe\FmLib\Service\RankingSystemServiceInterface;


/**
 * Class RankingController
 * @package App\Http\Controllers
 */
class RankingController extends BaseController
{
//<editor-fold desc="Public Methods">

  /**
   * @param RankingSystemServiceInterface $rss
   * @return JsonResponse
   */
  public function rankings(RankingSystemServiceInterface $rss): JsonResponse
  {
    ignore_user_abort(true);
    $rss->recalculateRankingSystems();
    $this->getEntityManager()->flush();
    $qb = $this->getEntityManager()->createQueryBuilder();
    $result = $qb->from(RankingSystemListEntry::class, 'rse')
      ->select('rse.points AS points')
      ->addSelect('rse.numberRankedEntities AS nGames')
      ->addSelect('rse.subClassData AS subClassData')
      ->addSelect('p.firstName AS firstName')
      ->addSelect('p.lastName AS lastName')
      ->addSelect('p.id AS playerId')
      ->addSelect('GROUP_CONCAT(mp.id) AS mergedPlayerIds')
      ->innerJoin('rse.player', 'p')
      ->innerJoin('rse.rankingSystemList', 'l')
      ->leftJoin('p.mergedPlayers', 'mp')
      ->where('l.current = 1')
      ->groupBy('rse.id')
      ->getQuery()->getResult();


    return response()->json($result);
  }
//</editor-fold desc="Public Methods">
}