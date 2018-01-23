<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/4/17
 * Time: 10:49 PM
 */

namespace App\Entity;


use App\Entity\Helpers\ResultEntity;
use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Helpers\TournamentHierarchyInterface;
use App\Helpers\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Phase
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="games")
 */
class Game extends TournamentHierarchyEntity
{
  use ResultEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="Match", inversedBy="games")
   * @var Match
   */
  private $match;

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersA",
   *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="player_id")})
   * @var Collection|Player
   */
  private $playersA;

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersB",
   *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="player_id")})
   * @var Collection|Player
   */
  private $playersB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $gameNumber;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Match constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->playersA = new ArrayCollection();
    $this->playersB = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return new ArrayCollection();
  }

  /**
   * @return int
   */
  public function getGameNumber(): int
  {
    return $this->gameNumber;
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::GAME;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getGameNumber();
  }

  /**
   * @return Match
   */
  public function getMatch(): Match
  {
    return $this->match;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return $this->getMatch();
  }

  /**
   * @return Player[]|Collection
   */
  public function getPlayersA()
  {
    return $this->playersA;
  }

  /**
   * @return Player[]|Collection
   */
  public function getPlayersB()
  {
    return $this->playersB;
  }

  /**
   * @param int $gameNumber
   * @return $this|Game
   */
  public function setGameNumber(int $gameNumber): Game
  {
    $this->gameNumber = $gameNumber;
    return $this;
  }

  /**
   * @param Match $match
   * @return $this|Game
   */
  public function setMatch(Match $match): Game
  {
    if ($this->match !== null) {
      $this->match->getGames()->remove($this->getGameNumber());
    }
    $this->match = $match;
    $match->getGames()->set($this->getGameNumber(), $this);
    return $this;
  }
//</editor-fold desc="Public Methods">
}