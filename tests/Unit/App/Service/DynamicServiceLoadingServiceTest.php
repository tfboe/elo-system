<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 3:53 PM
 */

namespace Tests\Tfboe\FmLib\Service;

use Tfboe\FmLib\Service\DynamicServiceLoadingService;
use Tfboe\FmLib\Service\RankingSystem\RankingSystemInterface;
use Illuminate\Contracts\Container\Container;
use Tests\Helpers\UnitTestCase;

/**
 * Class EloRankingTest
 * @package Tests\Tfboe\FmLib\Service
 */
class DynamicServiceLoadingServiceTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \Tfboe\FmLib\Service\DynamicServiceLoadingService::__construct
   */
  public function testConstruct()
  {
    $app = $this->getMockForAbstractClass(Container::class);
    /** @var Container $app */
    $entity = new DynamicServiceLoadingService($app);
    self::assertInstanceOf(DynamicServiceLoadingService::class, $entity);
    /** @noinspection PhpUnhandledExceptionInspection */
    self::assertEquals($app, self::getProperty(get_class($entity), 'app')->getValue($entity));
  }


  /**
   * @covers \Tfboe\FmLib\Service\DynamicServiceLoadingService::loadRankingSystemService
   * @covers \Tfboe\FmLib\Service\DynamicServiceLoadingService::getClassWithNamespace
   * @uses   \Tfboe\FmLib\Service\DynamicServiceLoadingService::__construct
   */
  public function testLoadRankingSystemService()
  {
    $app = $this->getMockForAbstractClass(Container::class);
    $instance = $this->getMockForAbstractClass(RankingSystemInterface::class);
    $app->expects(self::exactly(4))->method('make')->with('Tfboe\FmLib\Service\RankingSystem\TestInterface')
      ->willReturn($instance);
    /** @var Container $app */
    $entity = new DynamicServiceLoadingService($app);
    self::assertTrue($instance === $entity->loadRankingSystemService("Test"));
    self::assertTrue($instance === $entity->loadRankingSystemService("TestInterface"));
    self::assertTrue($instance === $entity->loadRankingSystemService("Tfboe\FmLib\Service\RankingSystem\Test"));
    self::assertTrue($instance ===
      $entity->loadRankingSystemService("Tfboe\FmLib\Service\RankingSystem\TestInterface"));
  }
//</editor-fold desc="Public Methods">
}