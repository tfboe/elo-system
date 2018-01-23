<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/6/18
 * Time: 12:06 PM
 */

namespace Tests\Unit\App\Providers;


use App\Providers\AppServiceProvider;
use App\Service\DynamicServiceLoadingServiceInterface;
use App\Service\RankingSystem\EloRankingInterface;
use App\Service\RankingSystemServiceInterface;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Foundation\Application;
use Tests\Helpers\UnitTestCase;

/**
 * Class AppServiceProviderTest
 * @package Tests\Unit\App\Providers
 */
class AppServiceProviderTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Providers\AppServiceProvider::register
   * @uses   \App\Service\RankingSystemService::__construct
   * @uses   \App\Service\RankingSystem\RankingSystemService::__construct
   * @uses   \App\Service\DynamicServiceLoadingService::__construct
   * @uses   \App\Service\RankingSystem\EntityComparerByTimeStartTimeAndLocalIdentifier::__construct
   */
  public function testRegister()
  {
    $instances = [
      EntityManagerInterface::class => ['instance' => $this->getMockForAbstractClass(EntityManagerInterface::class)]
    ];
    $app = $this->getMockForAbstractClass(Application::class);
    $app->expects(static::once())->method('environment')->willReturn('testing');
    $app->expects(static::once())->method('register')->with(IdeHelperServiceProvider::class);
    $app->expects(self::exactly(3))->method('singleton')->willReturnCallback(
      function ($name, $callback) use (&$instances) {
        $instances[$name] = ['callback' => $callback];
      });
    $app->expects(self::exactly(7))->method('make')->willReturnCallback(
      function ($name) use (&$instances, $app) {
        if (!array_key_exists('instance', $instances[$name])) {
          $instances[$name]['instance'] = $instances[$name]['callback']($app);
        }
        return $instances[$name]['instance'];
      });
    /** @var Application $app */
    $provider = new AppServiceProvider($app);
    $provider->register();
    $classes = [
      EntityManagerInterface::class,
      DynamicServiceLoadingServiceInterface::class,
      RankingSystemServiceInterface::class,
      EloRankingInterface::class
    ];
    self::assertEquals($classes, array_keys($instances));
    foreach ($classes as $class) {
      self::assertInstanceOf($class, $app->make($class));
    }
  }
//</editor-fold desc="Public Methods">
}