<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/17
 * Time: 10:57 AM
 */

namespace App\Entity;

use App\Entity\CategoryTraits\GameMode;
use App\Entity\CategoryTraits\OrganizingMode;
use App\Entity\CategoryTraits\ScoreMode;
use App\Entity\CategoryTraits\Table;
use App\Entity\CategoryTraits\TeamMode;
use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\NameEntity;
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
 * @ORM\Table(name="phases")
 *
 * Method hint for getName, since it will never throw an exception (name gets initialized empty)
 * @method string getName()
 */
class Phase extends BaseEntity implements TreeStructureEntityInterface
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;
  use TimeEntity;
  use UUIDEntity;
  use NameEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="Competition", inversedBy="phases")
   * @var Competition
   */
  protected $competition;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  protected $phaseNumber;

  /**
   * @ORM\OneToMany(targetEntity="QualificationSystem", mappedBy="nextPhase")
   * @var Collection|QualificationSystem[]
   */
  protected $previousQualificationSystems;

  /**
   * @ORM\OneToMany(targetEntity="QualificationSystem", mappedBy="previousPhase")
   * @var Collection|QualificationSystem[]
   */
  protected $nextQualificationSystems;

  /**
   * @ORM\OneToMany(targetEntity="Ranking", mappedBy="group", indexBy="uniqueRank")
   * @var Collection|Ranking[]
   */
  protected $rankings;

  /**
   * @ORM\OneToMany(targetEntity="Match", mappedBy="phase", indexBy="matchNumber")
   * @var Collection|Match[]
   */
  protected $matches;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="RankingSystem",
   *     inversedBy="phases",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__phase_ranking_systems")
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
   * Competition constructor.
   */
  public function __construct()
  {
    $this->previousQualificationSystems = new ArrayCollection();
    $this->nextQualificationSystems = new ArrayCollection();
    $this->name = '';
    $this->rankings = new ArrayCollection();
    $this->matches = new ArrayCollection();
    $this->rankingSystems = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return Competition
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getCompetition(): Competition
  {
    $this->ensureNotNull('competition');
    return $this->competition;
  }

  /**
   * @return Match[]|Collection
   */
  public function getMatches()
  {
    return $this->matches;
  }

  /**
   * @return QualificationSystem[]|Collection
   */
  public function getNextQualificationSystems(): Collection
  {
    return $this->nextQualificationSystems;
  }

  /**
   * @return int
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getPhaseNumber(): int
  {
    $this->ensureNotNull('phaseNumber');
    return $this->phaseNumber;
  }

  /**
   * @return QualificationSystem[]|Collection
   */
  public function getPreviousQualificationSystems(): Collection
  {
    return $this->previousQualificationSystems;
  }

  /**
   * @return Ranking[]|Collection
   */
  public function getRankings()
  {
    return $this->rankings;
  }

  /**
   * @param Competition $competition
   * @return $this|Phase
   * @throws \App\Exceptions\ValueNotSet if the phase number is not set
   */
  public function setCompetition(Competition $competition): Phase
  {
    if ($this->competition !== null) {
      $this->competition->getPhases()->remove($this->getPhaseNumber());
    }
    $this->competition = $competition;
    $competition->getPhases()->set($this->getPhaseNumber(), $this);
    return $this;
  }

  /**
   * @param int $phaseNumber
   * @return $this|Phase
   */
  public function setPhaseNumber(int $phaseNumber): Phase
  {
    $this->phaseNumber = $phaseNumber;
    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TreeStructureEntityInterface
  {
    return $this->getCompetition();
  }

  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getMatches();
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::PHASE;
  }
//</editor-fold desc="Public Methods">
}