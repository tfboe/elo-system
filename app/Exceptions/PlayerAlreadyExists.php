<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:31 PM
 */

namespace App\Exceptions;


use App\Entity\Player;

/**
 * Class PlayerAlreadyExists
 * @package App\Exceptions
 */
class PlayerAlreadyExists extends AbstractException
{
//<editor-fold desc="Fields">
  /** @var  Player[] */
  private $players;
//</editor-fold desc="Fields">
//<editor-fold desc="Constructor">
  /**
   * PlayerAlreadyExists constructor.
   * @param Player[] $players list of already existing players
   */
  public function __construct(array $players)
  {
    $this->players = $players;
    parent::__construct("Some players do already exist!", 409);
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * Gets a json representation of the exception
   * @return array
   */
  public function getJsonMessage()
  {
    return [
      'message' => "Some players do already exist",
      'players' => array_map(function (Player $player) {
        return [
          "firstName" => $player->getFirstName(),
          "lastName" => $player->getLastName(),
          "id" => $player->getPlayerId(),
          "birthday" => $player->getBirthday()->format("Y-m-d")];
      }, $this->players)
    ];
  }
//</editor-fold desc="Public Methods">
}