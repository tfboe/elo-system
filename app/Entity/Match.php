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
 * @ORM\Table(name="matches")
 */
class Match extends BaseEntity
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
   * @ORM\ManyToOne(targetEntity="Phase", inversedBy="matches")
   * @var Phase
   */
  protected $phase;

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
   * @ORM\ManyToMany(targetEntity="Ranking", indexBy="uniqueRank")
   * @ORM\JoinTable(name="relation__match_rankingA")
   * @var Collection|Ranking
   */
  protected $rankingsA;

  /**
   * @ORM\ManyToMany(targetEntity="Ranking", indexBy="uniqueRank")
   * @ORM\JoinTable(name="relation__match_rankingB")
   * @var Collection|Ranking
   */
  protected $rankingsB;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $matchNumber;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Match constructor.
   */
  public function __construct()
  {
    $this->rankingsA = new ArrayCollection();
    $this->rankingsB = new ArrayCollection();
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
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getId(): string
  {
    $this->ensureNotNull('id');
    return $this->id;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getMatchNumber(): int
  {
    $this->ensureNotNull('matchNumber');
    return $this->matchNumber;
  }

  /**
   * @return Phase
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getPhase(): Phase
  {
    $this->ensureNotNull('phase');
    return $this->phase;
  }

  /**
   * @return Ranking|Collection
   */
  public function getRankingsA()
  {
    return $this->rankingsA;
  }

  /**
   * @return Ranking|Collection
   */
  public function getRankingsB()
  {
    return $this->rankingsB;
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
   * @param int $matchNumber
   * @return $this|Match
   */
  public function setMatchNumber(int $matchNumber): Match
  {
    $this->matchNumber = $matchNumber;
    return $this;
  }

  /**
   * @param Phase $phase
   * @return $this|Match
   * @throws \App\Exceptions\ValueNotSet the match number is not yet set
   */
  public function setPhase(Phase $phase): Match
  {
    if ($this->phase !== null) {
      $this->phase->getMatches()->remove($this->getMatchNumber());
    }
    $this->phase = $phase;
    $phase->getMatches()->set($this->getMatchNumber(), $this);
    return $this;
  }

  /**
   * @param mixed $startTime
   * @return $this|Match
   */
  public function setStartTime($startTime): Match
  {
    $this->startTime = $startTime;
    return $this;
  }
//</editor-fold desc="Public Methods">
}