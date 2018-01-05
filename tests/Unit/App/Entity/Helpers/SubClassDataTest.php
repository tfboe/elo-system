<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 12:29 PM
 */

namespace Tests\Unit\App\Entity\Helpers;


use App\Entity\Helpers\SubClassData;
use App\Exceptions\MethodNotExistingException;
use App\Exceptions\PropertyNotExistingException;
use Tests\Helpers\TestCase;

/**
 * Class SubClassDataTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class SubClassDataTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testAddPropertyIfNotExistent()
  {
    $e = $this->mock();
    $e->initSubClassData([]);
    self::assertFalse($e->hasProperty("prop"));
    $e->addPropertyIfNotExistent("prop", "default");
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("default", $e->getProp());
    $e->addPropertyIfNotExistent("prop", "other");
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("default", $e->getProp());
  }

  public function testCall()
  {
    $e = $this->mock();
    $e->initSubClassData(["Prop"]);
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertNull($e->getProp());
    /** @noinspection PhpUndefinedMethodInspection */
    $e->setProp("test");
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("test", $e->getProp());
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("test", $e->isProp());

  }

  public function testInitSubClassDataAndHasProperty()
  {
    $e = $this->mock();
    /** @noinspection SpellCheckingInspection */
    $e->initSubClassData(["TESTUPPER", "testlower"]);
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("testupper"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("TESTUPPER"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("TESTupper"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("testlower"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("TESTLOWER"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("TESTlower"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($e->hasProperty("notexisting"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($e->hasProperty("NOTEXISTING"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($e->hasProperty("NOTexistING"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($e->hasProperty("subClassData"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($e->hasProperty("SUBCLASSDATA"));
  }

  public function testMethodExists()
  {
    $e = $this->mock();
    $e->initSubClassData(["test"]);
    self::assertTrue($e->methodExists("methodExists"));
    self::assertFalse($e->methodExists("notExistingMethod"));
    self::assertFalse($e->methodExists("getProp"));
    self::assertFalse($e->methodExists("isProp"));
    self::assertFalse($e->methodExists("setProp"));
    self::assertTrue($e->methodExists("getTest"));
    self::assertTrue($e->methodExists("isTest"));
    self::assertTrue($e->methodExists("setTest"));
    $e->addPropertyIfNotExistent("prop", null);
    self::assertTrue($e->methodExists("getProp"));
    self::assertTrue($e->methodExists("isProp"));
    self::assertTrue($e->methodExists("setProp"));
  }

  public function testNotExistingMethodCall()
  {
    $e = $this->mock();
    $e->initSubClassData([]);
    $this->expectException(MethodNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($e) . " had no method notExistingMethod");
    /** @noinspection PhpUndefinedMethodInspection */
    $e->notExistingMethod();
  }

  public function testNotExistingMethodSetterNoArgument()
  {
    $e = $this->mock();
    $e->initSubClassData([]);
    $this->expectException(MethodNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($e) . " had no method setProp");
    /** @noinspection PhpUndefinedMethodInspection */
    $e->setProp();
  }

  public function testNotExistingPropertyGetCall()
  {
    $e = $this->mock();
    $e->initSubClassData([]);
    $this->expectException(PropertyNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($e) .
      " had no property prop via getProperty");
    /** @noinspection PhpUndefinedMethodInspection */
    $e->getProp();
  }

  public function testNotExistingPropertySetCall()
  {
    $e = $this->mock();
    $e->initSubClassData([]);
    $this->expectException(PropertyNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($e) .
      " had no property prop via setProperty");
    /** @noinspection PhpUndefinedMethodInspection */
    $e->setProp(5);
  }
//</editor-fold desc="Public Methods">

//<editor-fold desc="Private Methods">
  /**
   * @return \PHPUnit_Framework_MockObject_MockObject|SubClassData
   */
  private function mock(): \PHPUnit_Framework_MockObject_MockObject
  {
    return $this->getMockForTrait(SubClassData::class);
  }
//</editor-fold desc="Private Methods">
}