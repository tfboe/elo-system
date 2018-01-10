<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/20/17
 * Time: 12:30 PM
 */

namespace App\Entity;

use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\NameEntity;
use App\Entity\Helpers\UUIDEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Team
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="teams")
 *
 * Method hint for getName, since it will never throw an exception (name gets initialized empty)
 * @method string getName()
 */
class Team extends BaseEntity
{
  use UUIDEntity;
  use NameEntity;
//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToMany(targetEntity="Player", indexBy="playerId")
   * @ORM\JoinTable(name="relation__team_players",
   *      joinColumns={@ORM\JoinColumn(name="team_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="player_id", referencedColumnName="player_id")}
   *      )
   *
   * @var Collection|Player[]
   */
  protected $players;

  /**
   * @ORM\ManyToOne(targetEntity="Competition", inversedBy="teams")
   * @var Competition
   */
  protected $competition;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $rank;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $startNumber;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Team constructor.
   */
  public function __construct()
  {
    $this->players = new ArrayCollection();
    $this->name = "";
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return Competition
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getCompetition(): Competition
  {
    $this->ensureNotNull("competition");
    return $this->competition;
  }

  /**
   * @return Player[]|Collection
   */
  public function getPlayers()
  {
    return $this->players;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getRank(): int
  {
    $this->ensureNotNull("rank");
    return $this->rank;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getStartNumber(): int
  {
    $this->ensureNotNull("startNumber");
    return $this->startNumber;
  }

  /**
   * @param Competition $competition
   * @return $this|Team
   * @throws \App\Exceptions\ValueNotSet if the start number is not set
   */
  public function setCompetition(Competition $competition): Team
  {
    if ($this->competition !== null) {
      $this->competition->getTeams()->remove($this->getStartNumber());
    }
    $this->competition = $competition;
    $this->competition->getTeams()->set($this->getStartNumber(), $this);
    return $this;
  }

  /**
   * @param int $rank
   * @return $this|Team
   */
  public function setRank(int $rank): Team
  {
    $this->rank = $rank;
    return $this;
  }

  /**
   * @param int $startNumber
   * @return $this|Team
   */
  public function setStartNumber(int $startNumber): Team
  {
    $this->startNumber = $startNumber;
    return $this;
  }
//</editor-fold desc="Public Methods">
}