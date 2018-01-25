<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 12:29 PM
 */

namespace Tests\Tfboe\FmLib\Entity\Helpers;


use Tfboe\FmLib\Entity\Helpers\SubClassData;
use Tfboe\FmLib\Exceptions\MethodNotExistingException;
use Tfboe\FmLib\Exceptions\PropertyNotExistingException;
use Tests\Helpers\UnitTestCase;

/**
 * Class SubClassDataTest
 * @package Tests\Tfboe\FmLib\Entity\Helpers
 */
class SubClassDataTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::addPropertyIfNotExistent
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::getProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
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
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::getProperty
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::setProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
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
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::cloneSubClassDataFrom
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   */
  public function testCloneSubClassDataFrom()
  {
    $entity = $this->mock();
    $other = $this->getMockForTrait(SubClassData::class, [], get_class($entity));
    /** @var SubClassData $other */
    $other->initSubClassData(['test']);
    $entity->initSubClassData(['other']);
    self::assertFalse($entity->hasProperty('test'));
    self::assertTrue($entity->hasProperty('other'));
    $entity->cloneSubClassDataFrom($other);
    self::assertTrue($entity->hasProperty('test'));
    self::assertFalse($entity->hasProperty('other'));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
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
    self::assertTrue($entity->hasProperty("subClassData"));
    /** @noinspection SpellCheckingInspection */
    self::assertFalse($entity->hasProperty("SUBCLASSDATA"));
  }

  /**
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::methodExists
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::addPropertyIfNotExistent
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::hasProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
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
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Exceptions\MethodNotExistingException::__construct
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
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Exceptions\MethodNotExistingException::__construct
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
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::getProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Exceptions\PropertyNotExistingException::__construct
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
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::__call
   * @covers \Tfboe\FmLib\Entity\Helpers\SubClassData::setProperty
   * @uses   \Tfboe\FmLib\Entity\Helpers\SubClassData::initSubClassData
   * @uses   \Tfboe\FmLib\Exceptions\PropertyNotExistingException::__construct
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