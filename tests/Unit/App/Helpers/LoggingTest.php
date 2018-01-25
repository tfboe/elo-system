<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:03 AM
 */

namespace Tests\Tfboe\FmLib\Helpers;


use Tfboe\FmLib\Helpers\Logging;
use Tfboe\FmLib\Helpers\Logs;
use Tests\Helpers\UnitTestCase;


/**
 * Class BasicEnumTest
 * @package Tests\Tfboe\FmLib\Helpers
 */
class LoggingTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * Clears the testing log
   * @before
   */
  public function clearLog()
  {
    $path = sys_get_temp_dir() . '/logs/' . Logs::TESTING . '.log';
    if (is_writeable($path)) {
      unlink($path);
    }
  }

  /**
   * @covers \Tfboe\FmLib\Helpers\Logging::log
   * @uses   \Tfboe\FmLib\Helpers\BasicEnum
   */
  public function testLog()
  {
    /**
     * @return string
     */
    function storage_path_function()
    {
      return sys_get_temp_dir();
    }

    ;

    Logging::$storagePathFunction = 'Tests\Tfboe\FmLib\Helpers\storage_path_function';
    /** @noinspection PhpUnhandledExceptionInspection */
    Logging::log("invalid", "INVALID LOG");
    /** @noinspection PhpUnhandledExceptionInspection */
    Logging::log("testMessage", Logs::TESTING);
    $path = sys_get_temp_dir() . '/logs/' . Logs::TESTING . '.log';
    self::assertTrue(is_writable($path));
    self::assertRegExp('/^\\[[2-9][0-9]{3}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}\\] testing\\.INFO: ' .
      'testMessage \\[\\] \\[\\]\\n$/', file_get_contents($path));
    Logging::$storagePathFunction = 'storage_path';
  }
//</editor-fold desc="Public Methods">
}