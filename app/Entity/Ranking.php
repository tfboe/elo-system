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
use App\Exceptions\ValueNotSet;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Ranking
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankings")
 */
class Ranking extends BaseEntity
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
   * @ORM\ManyToMany(targetEntity="Team", indexBy="startNumber")
   * @ORM\JoinTable(name="relation__ranking_teams")
   * @var Collection|Team[]
   */
  protected $teams;

  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="rankings")
   * @var Phase
   */
  protected $phase;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $rank;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $uniqueRank;

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
    $this->teams = new ArrayCollection();
    $this->name = "";
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return Phase
   * @throws ValueNotSet
   */
  public function getPhase(): Phase
  {
    $this->ensureNotNull("phase");
    return $this->phase;
  }

  /**
   * @return string
   * @throws ValueNotSet
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
   * @return int
   * @throws ValueNotSet
   */
  public function getRank(): int
  {
    $this->ensureNotNull("rank");
    return $this->rank;
  }

  /**
   * @return Team[]|Collection
   */
  public function getTeams(): Collection
  {
    return $this->teams;
  }

  /**
   * @return int
   * @throws ValueNotSet
   */
  public function getUniqueRank(): int
  {
    $this->ensureNotNull("uniqueRank");
    return $this->uniqueRank;
  }

  /**
   * @param Phase $phase
   * @return $this|Ranking
   * @throws ValueNotSet if the unique rank is not set
   */
  public function setPhase(Phase $phase): Ranking
  {
    if ($this->phase !== null) {
      $this->phase->getRankings()->remove($this->getUniqueRank());
    }
    $this->phase = $phase;
    $phase->getRankings()->set($this->getUniqueRank(), $this);
    return $this;
  }

  /**
   * @param string $name
   * @return $this|Ranking
   */
  public function setName(string $name): Ranking
  {
    $this->name = $name;
    return $this;
  }

  /**
   * @param int $rank
   * @return $this|Ranking
   */
  public function setRank(int $rank): Ranking
  {
    $this->rank = $rank;
    return $this;
  }

  /**
   * @param int $uniqueRank
   * @return $this|Ranking
   */
  public function setUniqueRank(int $uniqueRank): Ranking
  {
    $this->uniqueRank = $uniqueRank;
    return $this;
  }
//</editor-fold desc="Public Methods">


}