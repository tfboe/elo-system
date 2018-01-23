<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/15/17
 * Time: 10:57 AM
 */

namespace App\Entity;


use App\Entity\Helpers\NameEntity;
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
 * @ORM\Table(name="phases")
 *
 * Method hint for getName, since it will never throw an exception (name gets initialized empty)
 * @method string getName()
 */
class Phase extends TournamentHierarchyEntity
{
  use NameEntity;

//<editor-fold desc="Fields">

  /**
   * @ORM\ManyToOne(targetEntity="Competition", inversedBy="phases")
   * @var Competition
   */
  private $competition;

  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $phaseNumber;

  /**
   * @ORM\OneToMany(targetEntity="QualificationSystem", mappedBy="nextPhase")
   * @var Collection|QualificationSystem[]
   */
  private $preQualifications;

  /**
   * @ORM\OneToMany(targetEntity="QualificationSystem", mappedBy="previousPhase")
   * @var Collection|QualificationSystem[]
   */
  private $postQualifications;

  /**
   * @ORM\OneToMany(targetEntity="Ranking", mappedBy="group", indexBy="uniqueRank")
   * @var Collection|Ranking[]
   */
  private $rankings;

  /**
   * @ORM\OneToMany(targetEntity="Match", mappedBy="phase", indexBy="matchNumber")
   * @var Collection|Match[]
   */
  private $matches;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Competition constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->preQualifications = new ArrayCollection();
    $this->postQualifications = new ArrayCollection();
    $this->name = '';
    $this->rankings = new ArrayCollection();
    $this->matches = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getMatches();
  }

  /**
   * @return Competition
   */
  public function getCompetition(): Competition
  {
    return $this->competition;
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::PHASE;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getPhaseNumber();
  }

  /**
   * @return Match[]|Collection
   */
  public function getMatches()
  {
    return $this->matches;
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TournamentHierarchyInterface
  {
    return $this->getCompetition();
  }

  /**
   * @return int
   */
  public function getPhaseNumber(): int
  {
    return $this->phaseNumber;
  }

  /**
   * @return QualificationSystem[]|Collection
   */
  public function getPostQualifications(): Collection
  {
    return $this->postQualifications;
  }

  /**
   * @return QualificationSystem[]|Collection
   */
  public function getPreQualifications(): Collection
  {
    return $this->preQualifications;
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
//</editor-fold desc="Public Methods">
}