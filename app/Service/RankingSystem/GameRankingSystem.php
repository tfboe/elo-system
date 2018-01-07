<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 2:34 PM
 */

namespace App\Service\RankingSystem;


use App\Entity\Game;
use App\Helpers\Level;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * Class GameRankingSystem
 * @package App\Service\RankingSystem
 */
abstract class GameRankingSystem extends RankingSystem implements GameRankingSystemInterface
{
//<editor-fold desc="Protected Methods">
  /**
   * @inheritDoc
   */
  protected function getEntitiesQueryBuilder(\App\Entity\RankingSystem $ranking, \DateTime $from): QueryBuilder
  {
    // query all relevant games
    $query = $this->em->createQueryBuilder();
    $query
      ->from(Game::class, 'g')
      ->select('g')
      ->leftJoin('g.rankingSystems', 'grs', Query\Expr\Join::WITH, $query->expr()->eq('grs', ':ranking'))
      ->innerJoin('g.match', 'm')
      ->leftJoin('m.rankingSystems', 'mrs', Query\Expr\Join::WITH, $query->expr()->eq('mrs', ':ranking'))
      ->innerJoin('m.phase', 'p')
      ->leftJoin('p.rankingSystems', 'prs', Query\Expr\Join::WITH, $query->expr()->eq('prs', ':ranking'))
      ->innerJoin('p.competition', 'c')
      ->leftJoin('c.rankingSystems', 'crs', Query\Expr\Join::WITH, $query->expr()->eq('crs', ':ranking'))
      ->innerJoin('c.tournament', 't')
      ->leftJoin('t.rankingSystems', 'trs', Query\Expr\Join::WITH, $query->expr()->eq('trs', ':ranking'))
      ->setParameter('ranking', $ranking)
      ->setParameter('from', $from);
    $times = ['g.endTime', 'g.startTime', 'm.endTime', 'm.startTime', 'p.endTime', 'p.startTime', 'c.endTime',
      'c.startTime', 't.endTime', 't.startTime', 't.updatedAt'];
    $or_expr = $query->expr()->orX();
    for ($i = 0; $i < count($times); $i++) {
      $expr = $query->expr()->gt($times[$i], ':from');
      if ($i > 0) {
        $expr = $query->expr()->andX($expr);
        for ($j = 0; $j < $i; $j++) {
          $expr->add($query->expr()->isNull($times[$i]));
        }
      }
      $or_expr->add($expr);
    }
    $query->andWhere($or_expr);
    $query->andWhere($query->expr()->orX(
      $query->expr()->isNotNull('grs.id'),
      $query->expr()->isNotNull('mrs.id'),
      $query->expr()->isNotNull('prs.id'),
      $query->expr()->isNotNull('crs.id'),
      $query->expr()->isNotNull('trs.id')
    ));

    return $query;
  }

  /**
   * @inheritDoc
   */
  protected function getLevel(): int
  {
    return Level::GAME;
  }
//</editor-fold desc="Protected Methods">

}