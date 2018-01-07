<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 12:52 PM
 */

namespace Tests\Unit\App\Entity\Helpers;

use App\Entity\Helpers\IdGenerator;
use Doctrine\ORM\EntityManager;
use Tests\Helpers\UnitTestCase;

/**
 * Class IdGeneratorTest
 * @package Tests\Unit\App\Entity\Helpers
 */
class IdGeneratorTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">

  /**
   * @covers \App\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testCreateIdFrom()
  {
    /**
     * test function for generating an guid
     * @return string fixed test string
     */
    function test_com_create_guid()
    {
      return "{test-guid}";
    }

    self::assertEquals('test-guid',
      IdGenerator::createIdFrom('Tests\Unit\App\Entity\Helpers\test_com_create_guid'));

    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', IdGenerator::createIdFrom());
  }

  /**
   * @covers \App\Entity\Helpers\IdGenerator::generate
   * @uses   \App\Entity\Helpers\IdGenerator::createIdFrom
   */
  public function testGenerate()
  {
    $generator = new IdGenerator();
    $em = $this->createMock(EntityManager::class);
    /** @var EntityManager $em */
    self::assertRegExp('/^[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}$/', $generator->generate($em, null));
  }
//</editor-fold desc="Public Methods">
}