<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Tfboe\FmLib\Exceptions;


use Tfboe\FmLib\Entity\Player;
use Tfboe\FmLib\Exceptions\PlayerAlreadyExists;
use Tests\Helpers\UnitTestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Tfboe\FmLib\Exceptions
 */
class PlayerAlreadyExistsTest extends UnitTestCase
{
//<editor-fold desc="Public Methods">
  /**
   * @covers \Tfboe\FmLib\Exceptions\PlayerAlreadyExists::__construct
   */
  public function testConstructor()
  {
    $exc = new PlayerAlreadyExists([]);
    self::assertEquals($exc->getMessage(), "Some players do already exist!");
    self::assertEquals(409, $exc->getCode());
  }

  /**
   * @covers \Tfboe\FmLib\Exceptions\PlayerAlreadyExists::getJsonMessage
   * @uses   \Tfboe\FmLib\Exceptions\PlayerAlreadyExists::__construct
   * @uses   \Tfboe\FmLib\Entity\Player
   */
  public function testJsonMessage()
  {
    $exc = new PlayerAlreadyExists([]);
    self::assertEquals(['message' => 'Some players do already exist', 'players' => []], $exc->getJsonMessage());

    $player = new Player();
    $player->setFirstName('first');
    $player->setLastName('last');
    $player->setBirthday(new \DateTime('1990-02-02'));
    /** @noinspection PhpUnhandledExceptionInspection */
    /** @noinspection PhpUnhandledExceptionInspection */
    $idProperty = self::getProperty(Player::class, 'playerId');
    $idProperty->setValue($player, 0);

    $exc2 = new PlayerAlreadyExists([$player]);
    self::assertEquals(['message' => 'Some players do already exist', 'players' => [['firstName' => 'first',
      'lastName' => 'last', 'id' => 0, 'birthday' => '1990-02-02']]], $exc2->getJsonMessage());

    $player2 = new Player();
    $player2->setFirstName('first2');
    $player2->setLastName('last2');
    $player2->setBirthday(new \DateTime('1992-04-04'));
    $idProperty->setValue($player2, 1);


    $exc3 = new PlayerAlreadyExists([$player, $player2]);
    self::assertEquals(['message' => 'Some players do already exist', 'players' => [
      ['firstName' => 'first', 'lastName' => 'last', 'id' => 0, 'birthday' => '1990-02-02'],
      ['firstName' => 'first2', 'lastName' => 'last2', 'id' => 1, 'birthday' => '1992-04-04']]],
      $exc3->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}