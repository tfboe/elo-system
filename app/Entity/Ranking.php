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
 * Class Ranking
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankings")
 *
 * Method hint for getName, since it will never throw an exception (name gets initialized empty)
 * @method string getName()
 */
class Ranking extends BaseEntity
{
  use UUIDEntity;
  use NameEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToMany(targetEntity="Team", indexBy="startNumber")
   * @ORM\JoinTable(name="relation__ranking_teams")
   * @var Collection|Team[]
   */
  private $teams;

  /**
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="rankings")
   * @var Phase
   */
  private $phase;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $rank;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $uniqueRank;
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
   */
  public function getPhase(): Phase
  {
    return $this->phase;
  }

  /**
   * @return int
   */
  public function getRank(): int
  {
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
   */
  public function getUniqueRank(): int
  {
    return $this->uniqueRank;
  }

  /**
   * @param Phase $phase
   * @return $this|Ranking
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