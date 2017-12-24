<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/4/17
 * Time: 10:49 PM
 */

namespace App\Entity;

use App\Entity\CategoryTraits\GameMode;
use App\Entity\CategoryTraits\OrganizingMode;
use App\Entity\CategoryTraits\ScoreMode;
use App\Entity\CategoryTraits\Table;
use App\Entity\CategoryTraits\TeamMode;
use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\ResultEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Phase
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="games")
 */
class Game extends BaseEntity
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;
  use ResultEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="CUSTOM")
   * @ORM\CustomIdGenerator(class="App\Entity\Helpers\IdGenerator")
   * @ORM\Column(type="guid")
   * @var string
   */
  protected $id;

  /**
   * @ORM\ManyToOne(targetEntity="Match", inversedBy="games")
   * @var Match
   */
  protected $match;

  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var ?\DateTime
   */
  protected $startTime;

  /**
   * @ORM\Column(type="datetimetz", nullable=true)
   * @var ?\DateTime
   */
  protected $endTime;

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersA")
   * @var Collection|Player
   */
  protected $playersA;

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersB")
   * @var Collection|Player
   */
  protected $playersB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $gameNumber;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Match constructor.
   */
  public function __construct()
  {
    $this->playersA = new ArrayCollection();
    $this->playersB = new ArrayCollection();
    $this->endTime = null;
    $this->startTime = null;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return \DateTime|null
   */
  public function getEndTime(): ?\DateTime
  {
    return $this->endTime;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getGameNumber(): int
  {
    $this->ensureNotNull('gameNumber');
    return $this->gameNumber;
  }

  /**
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getId(): string
  {
    $this->ensureNotNull('id');
    return $this->id;
  }

  /**
   * @return Match
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getMatch(): Match
  {
    $this->ensureNotNull('match');
    return $this->match;
  }

  /**
   * @return Player|Collection
   */
  public function getPlayersA()
  {
    return $this->playersA;
  }

  /**
   * @return Player|Collection
   */
  public function getPlayersB()
  {
    return $this->playersB;
  }

  /**
   * @return \DateTime|null
   */
  public function getStartTime(): ?\DateTime
  {
    return $this->startTime;
  }

  /**
   * @param mixed $endTime
   * @return $this|Match
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
    return $this;
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
   * @throws \App\Exceptions\ValueNotSet the game number is not yet set
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

  /**
   * @param mixed $startTime
   * @return $this|Game
   */
  public function setStartTime($startTime): Game
  {
    $this->startTime = $startTime;
    return $this;
  }
//</editor-fold desc="Public Methods">
}