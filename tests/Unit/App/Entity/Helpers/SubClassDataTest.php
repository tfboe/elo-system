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
use Tests\Helpers\UnitTestCase;

/**
 * Class SubClassDataTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class SubClassDataTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \App\Entity\Helpers\SubClassData::addPropertyIfNotExistent
   * @uses   \App\Entity\Helpers\SubClassData::__call
   * @uses   \App\Entity\Helpers\SubClassData::getProperty
   * @uses   \App\Entity\Helpers\SubClassData::hasProperty
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testAddPropertyIfNotExistent()
  {
    $entity = $this->mock();
    $entity->initSubClassData([]);
    self::assertFalse($entity->hasProperty("prop"));
    $entity->addPropertyIfNotExistent("prop", "default");
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("default", $entity->getProp());
    $entity->addPropertyIfNotExistent("prop", "other");
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("default", $entity->getProp());
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::__call
   * @covers \App\Entity\Helpers\SubClassData::getProperty
   * @covers \App\Entity\Helpers\SubClassData::setProperty
   * @uses   \App\Entity\Helpers\SubClassData::hasProperty
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testCall()
  {
    $entity = $this->mock();
    $entity->initSubClassData(["Prop"]);
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertNull($entity->getProp());
    /** @noinspection PhpUndefinedMethodInspection */
    $entity->setProp("test");
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("test", $entity->getProp());
    /** @noinspection PhpUndefinedMethodInspection */
    self::assertEquals("test", $entity->isProp());
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::initSubClassData
   * @covers \App\Entity\Helpers\SubClassData::hasProperty
   */
  public function testInitSubClassDataAndHasProperty()
  {
    $entity = $this->mock();
    /** @noinspection SpellCheckingInspection */
    $entity->initSubClassData(["TESTUPPER", "testlower"]);
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("testupper"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("TESTUPPER"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("TESTupper"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("testlower"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("TESTLOWER"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("TESTlower"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($entity->hasProperty("notexisting"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($entity->hasProperty("NOTEXISTING"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($entity->hasProperty("NOTexistING"));
    /** @noinspection SpellCheckingInspection */
    self::assertTrue($entity->hasProperty("subClassData"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($entity->hasProperty("SUBCLASSDATA"));
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::methodExists
   * @uses   \App\Entity\Helpers\SubClassData::addPropertyIfNotExistent
   * @uses   \App\Entity\Helpers\SubClassData::hasProperty
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testMethodExists()
  {
    $entity = $this->mock();
    $entity->initSubClassData(["test"]);
    self::assertTrue($entity->methodExists("methodExists"));
    self::assertFalse($entity->methodExists("notExistingMethod"));
    self::assertFalse($entity->methodExists("getProp"));
    self::assertFalse($entity->methodExists("isProp"));
    self::assertFalse($entity->methodExists("setProp"));
    self::assertTrue($entity->methodExists("getTest"));
    self::assertTrue($entity->methodExists("isTest"));
    self::assertTrue($entity->methodExists("setTest"));
    $entity->addPropertyIfNotExistent("prop", null);
    self::assertTrue($entity->methodExists("getProp"));
    self::assertTrue($entity->methodExists("isProp"));
    self::assertTrue($entity->methodExists("setProp"));
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::__call
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Exceptions\MethodNotExistingException::__construct
   */
  public function testNotExistingMethodCall()
  {
    $entity = $this->mock();
    $entity->initSubClassData([]);
    $this->expectException(MethodNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($entity) . " had no method notExistingMethod");
    /** @noinspection PhpUndefinedMethodInspection */
    $entity->notExistingMethod();
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::__call
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Exceptions\MethodNotExistingException::__construct
   */
  public function testNotExistingMethodSetterNoArgument()
  {
    $entity = $this->mock();
    $entity->initSubClassData([]);
    $this->expectException(MethodNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($entity) . " had no method setProp");
    /** @noinspection PhpUndefinedMethodInspection */
    $entity->setProp();
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::__call
   * @covers \App\Entity\Helpers\SubClassData::getProperty
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Exceptions\PropertyNotExistingException::__construct
   */
  public function testNotExistingPropertyGetCall()
  {
    $entity = $this->mock();
    $entity->initSubClassData([]);
    $this->expectException(PropertyNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($entity) .
      " had no property prop via getProperty");
    /** @noinspection PhpUndefinedMethodInspection */
    $entity->getProp();
  }

  /**
   * @covers \App\Entity\Helpers\SubClassData::__call
   * @covers \App\Entity\Helpers\SubClassData::setProperty
   * @uses   \App\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \App\Exceptions\PropertyNotExistingException::__construct
   */
  public function testNotExistingPropertySetCall()
  {
    $entity = $this->mock();
    $entity->initSubClassData([]);
    $this->expectException(PropertyNotExistingException::class);
    $this->expectExceptionMessage("An object of the class " . get_class($entity) .
      " had no property prop via setProperty");
    /** @noinspection PhpUndefinedMethodInspection */
    $entity->setProp(5);
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