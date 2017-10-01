<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Exceptions\ValueNotSet;
use Tests\Helpers\TestCase;

/**
 * Class ValueNotSetTest
 * @package Tests\Unit\App\Exceptions
 */
class ValueNotSetTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $exc = new ValueNotSet("MyClass", "MyProperty");
    self::assertEquals('The property MyProperty of the class MyClass must be set before it can be accessed. ' .
      'Please set the property immediately after you call the constructor(Empty Constructor Pattern).',
      $exc->getMessage());
  }
//</editor-fold desc="Public Methods">
}