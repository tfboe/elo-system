<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 2:08 PM
 */

namespace Tests\Unit\App\Exceptions;


use App\Entity\Player;
use App\Exceptions\PlayerAlreadyExists;
use Tests\Helpers\TestCase;

/**
 * Class ValueNotValidTest
 * @package Tests\Unit\App\Exceptions
 */
class PlayerAlreadyExistsTest extends TestCase
{
//<editor-fold desc="Public Methods">
  public function testConstructor()
  {
    $exc = new PlayerAlreadyExists([]);
    self::assertEquals($exc->getMessage(), "Some players do already exist!");
    self::assertEquals(409, $exc->getCode());
  }


  public function testJsonMessage()
  {
    $exc = new PlayerAlreadyExists([]);
    self::assertEquals(['message' => 'Some players do already exist', 'players' => []], $exc->getJsonMessage());

    $player = new Player();
    $player->setFirstName('first');
    $player->setLastName('last');
    $player->setBirthday(new \DateTime('1990-02-02'));
    $id_property = self::getProperty(Player::class, 'id');
    $id_property->setValue($player, 0);

    $exc2 = new PlayerAlreadyExists([$player]);
    self::assertEquals(['message' => 'Some players do already exist', 'players' => [['firstName' => 'first',
      'lastName' => 'last', 'id' => 0, 'birthday' => '1990-02-02']]], $exc2->getJsonMessage());

    $player2 = new Player();
    $player2->setFirstName('first2');
    $player2->setLastName('last2');
    $player2->setBirthday(new \DateTime('1992-04-04'));
    $id_property->setValue($player2, 1);


    $exc3 = new PlayerAlreadyExists([$player, $player2]);
    self::assertEquals(['message' => 'Some players do already exist', 'players' => [
      ['firstName' => 'first', 'lastName' => 'last', 'id' => 0, 'birthday' => '1990-02-02'],
      ['firstName' => 'first2', 'lastName' => 'last2', 'id' => 1, 'birthday' => '1992-04-04']]],
      $exc3->getJsonMessage());
  }
//</editor-fold desc="Public Methods">
}