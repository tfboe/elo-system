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
use Illuminate\Contracts\Foundation\Application;
use Tfboe\FmLib\TestHelpers\UnitTestCase;

/**
 * Class AppServiceProviderTest
 * @package Tests\Tfboe\FmLib\Providers
 */
class AppServiceProviderTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Providers\AppServiceProvider::register
   */
  public function testRegister()
  {
    $app = $this->getMockForAbstractClass(Application::class);
    $app->expects(static::exactly(4))->method('register');
    /** @var Application $app */
    $provider = new AppServiceProvider($app);
    $provider->register();
  }
//</editor-fold desc="Public Methods">
}