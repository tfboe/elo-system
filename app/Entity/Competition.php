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
 * Class Competition
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="competitions",indexes={@ORM\Index(name="unique_name_idx", columns={"tournament_id","name"})})
 */
class Competition extends BaseEntity implements TreeStructureEntityInterface
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
   * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="competitions")
   * @var Tournament
   */
  protected $tournament;

  /**
   * @ORM\OneToMany(targetEntity="Team", mappedBy="competition", indexBy="startNumber")
   * @var Collection|Team[]
   */
  protected $teams;

  /**
   * @ORM\OneToMany(targetEntity="Phase", mappedBy="competition", indexBy="phaseNumber")
   * @var Collection|Phase[]
   */
  protected $phases;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="RankingSystem",
   *     inversedBy="competitions",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__competition_ranking_systems")
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
    $this->teams = new ArrayCollection();
    $this->phases = new ArrayCollection();
    $this->rankingSystems = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return Phase[]|Collection
   */
  public function getPhases()
  {
    return $this->phases;
  }

  /**
   * @return Team[]|Collection
   */
  public function getTeams()
  {
    return $this->teams;
  }

  /**
   * @return Tournament
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getTournament(): Tournament
  {
    $this->ensureNotNull('tournament');
    return $this->tournament;
  }

  /**
   * @param Tournament $tournament
   * @return $this|Competition
   * @throws \App\Exceptions\ValueNotSet if the name is not set
   */
  public function setTournament(Tournament $tournament): Competition
  {
    if ($this->tournament !== null) {
      $this->tournament->getCompetitions()->remove($this->getName());
    }
    $this->tournament = $tournament;
    $tournament->getCompetitions()->set($this->getName(), $this);
    return $this;
  }
//</editor-fold desc="Public Methods">

  /**
   * @inheritDoc
   */
  public function getParent(): ?TreeStructureEntityInterface
  {
    return $this->getTournament();
  }

  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getPhases();
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::COMPETITION;
  }
}