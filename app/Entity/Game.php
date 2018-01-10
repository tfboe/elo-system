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
use App\Entity\Helpers\TimeEntity;
use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Entity\Helpers\UUIDEntity;
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
class Game extends BaseEntity implements TreeStructureEntityInterface
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;
  use ResultEntity;
  use TimeEntity;
  use UUIDEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="Match", inversedBy="games")
   * @var Match
   */
  protected $match;

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersA",
   *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="player_id")})
   * @var Collection|Player
   */
  protected $playersA;

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="id")
   * @ORM\JoinTable(name="relation__game_playersB",
   *      joinColumns={@ORM\JoinColumn(name="game_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="player_id")})
   * @var Collection|Player
   */
  protected $playersB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $gameNumber;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="RankingSystem",
   *     inversedBy="matches",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__game_ranking_systems")
   * @var Collection|RankingSystem[]
   */
  private $rankingSystems;

  /**
   * @return RankingSystem[]|Collection
   */
  public function getRankingSystems()
  {
    return $this->rankingSystems;
  }
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Match constructor.
   */
  public function __construct()
  {
    $this->playersA = new ArrayCollection();
    $this->playersB = new ArrayCollection();
    $this->rankingSystems = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">

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
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getGameNumber();
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
   * @inheritDoc
   */
  public function getParent(): ?TreeStructureEntityInterface
  {
    return $this->getMatch();
  }

  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return new ArrayCollection();
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::GAME;
  }
//</editor-fold desc="Public Methods">
}