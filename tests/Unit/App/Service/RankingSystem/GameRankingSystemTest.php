<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tests\Unit\App\Service\RankingSystem;

use App\Entity\RankingSystem;
use App\Helpers\Level;
use App\Service\RankingSystem\GameRankingSystem;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Tests\Helpers\UnitTestCase;

/**
 * Class GameRankingSystemTest
 * @package Tests\Unit\App\Service\RankingSystem
 */
class GameRankingSystemTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Service\RankingSystem\GameRankingSystem::getLevel
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testLevel()
  {
    $e = $this->getMockForAbstractClass(GameRankingSystem::class,
      [$this->createMock(EntityManagerInterface::class)]);
    self::assertEquals(Level::GAME, self::callProtectedMethod($e, "getLevel"));
  }

  /**
   * @covers \App\Service\RankingSystem\GameRankingSystem::getEntitiesQueryBuilder
   * @uses   \App\Service\RankingSystem\RankingSystem::__construct
   */
  public function testGetEntitiesQueryBuilder()
  {
    $em = $this->getMockForAbstractClass(EntityManager::class, [], '', false);
    $e = $this->getMockForAbstractClass(GameRankingSystem::class, [$em]);
    $ranking_system = $this->createMock(RankingSystem::class);
    $ranking_system->method('getId')->willReturn('ranking-system-id');
    /** @var QueryBuilder $builder */
    $builder = self::callProtectedMethod($e, "getEntitiesQueryBuilder", [$ranking_system, new \DateTime("2000-01-01")]);
    /** @noinspection LongLine */
    self::assertEquals(
      'SELECT g FROM App\Entity\Game g LEFT JOIN g.rankingSystems grs WITH grs = :ranking INNER JOIN g.match ' .
      'm LEFT JOIN m.rankingSystems mrs WITH mrs = :ranking INNER JOIN m.phase p LEFT JOIN p.rankingSystems prs WITH ' .
      'prs = :ranking INNER JOIN p.competition c LEFT JOIN c.rankingSystems crs WITH crs = :ranking INNER JOIN ' .
      'c.tournament t LEFT JOIN t.rankingSystems trs WITH trs = :ranking WHERE (g.endTime > :from OR (g.startTime > ' .
      ':from AND g.startTime IS NULL) OR (m.endTime > :from AND m.endTime IS NULL AND m.endTime IS NULL) OR ' .
      '(m.startTime > :from AND m.startTime IS NULL AND m.startTime IS NULL AND m.startTime IS NULL) OR (p.endTime ' .
      '> :from AND p.endTime IS NULL AND p.endTime IS NULL AND p.endTime IS NULL AND p.endTime IS NULL) OR ' .
      '(p.startTime > :from AND p.startTime IS NULL AND p.startTime IS NULL AND p.startTime IS NULL AND p.startTime ' .
      'IS NULL AND p.startTime IS NULL) OR (c.endTime > :from AND c.endTime IS NULL AND c.endTime IS NULL AND ' .
      'c.endTime IS NULL AND c.endTime IS NULL AND c.endTime IS NULL AND c.endTime IS NULL) OR (c.startTime > :from ' .
      'AND c.startTime IS NULL AND c.startTime IS NULL AND c.startTime IS NULL AND c.startTime IS NULL AND ' .
      'c.startTime IS NULL AND c.startTime IS NULL AND c.startTime IS NULL) OR (t.endTime > :from AND t.endTime IS ' .
      'NULL AND t.endTime IS NULL AND t.endTime IS NULL AND t.endTime IS NULL AND t.endTime IS NULL AND t.endTime IS ' .
      'NULL AND t.endTime IS NULL AND t.endTime IS NULL) OR (t.startTime > :from AND t.startTime IS NULL AND ' .
      't.startTime IS NULL AND t.startTime IS NULL AND t.startTime IS NULL AND t.startTime IS NULL AND t.startTime IS' .
      ' NULL AND t.startTime IS NULL AND t.startTime IS NULL AND t.startTime IS NULL) OR (t.updatedAt > :from AND ' .
      't.updatedAt IS NULL AND t.updatedAt IS NULL AND t.updatedAt IS NULL AND t.updatedAt IS NULL AND t.updatedAt IS' .
      ' NULL AND t.updatedAt IS NULL AND t.updatedAt IS NULL AND t.updatedAt IS NULL AND t.updatedAt IS NULL AND ' .
      't.updatedAt IS NULL)) AND (grs.id IS NOT NULL OR mrs.id IS NOT NULL OR prs.id IS NOT NULL OR crs.id IS NOT ' .
      'NULL OR trs.id IS NOT NULL)',
      $builder->getDQL());
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
//</editor-fold desc="Private Methods">
}