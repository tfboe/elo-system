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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Team
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="teams")
 */
class Team extends BaseEntity
{
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
   * @ORM\ManyToMany(targetEntity="Player")
   * @ORM\JoinTable(name="relation__team_players")
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

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $name;
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
   */
  public function getCompetition(): Competition
  {
    $this->ensureNotNull("competition");
    return $this->competition;
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    $this->ensureNotNull("id");
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    return $this->name;
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
   */
  public function getRank(): int
  {
    $this->ensureNotNull("rank");
    return $this->rank;
  }

  /**
   * @return int
   */
  public function getStartNumber(): int
  {
    $this->ensureNotNull("startNumber");
    return $this->startNumber;
  }

  /**
   * @param Competition $competition
   * @return $this|Team
   */
  public function setCompetition(Competition $competition): Team
  {
    $this->competition = $competition;
    $this->competition->getTeams()->set($this->getStartNumber(), $this);
    return $this;
  }

  /**
   * @param string $name
   * @return $this|Team
   */
  public function setName(string $name): Team
  {
    $this->name = $name;
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