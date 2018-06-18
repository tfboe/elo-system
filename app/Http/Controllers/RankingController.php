<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 8:45 PM
 */

namespace App\Http\Controllers;


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
   * @return JsonResponse
   */
  public function rankings(RankingSystemServiceInterface $rss): JsonResponse
  {
    ignore_user_abort(true);
    $rss->recalculateRankingSystems();
    $this->getEntityManager()->flush();
    /*$qb = $this->getEntityManager()->createQueryBuilder();
    $result = $qb->from(RankingSystemListEntry::class, 'rse')
      ->select('rse.points AS points')
      ->addSelect('rse.numberRankedEntities AS totalTournaments')
      ->addSelect('rse.subClassData AS subClassData')
      ->addSelect('p.firstName AS firstName')
      ->addSelect('p.lastName AS lastName')
      ->innerJoin('rse.player', 'p')
      ->innerJoin('rse.rankingSystemList', 'l')
      ->where('l.current = 1')
      ->getQuery()->getArrayResult();*/
    //TODO

    return response()->json(true);
  }
//</editor-fold desc="Public Methods">
}