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
use App\Entity\Helpers\TimeEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Competition
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="competitions",indexes={@ORM\Index(name="unique_name_idx", columns={"tournament_id","name"})})
 */
class Competition extends BaseEntity
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;
  use TimeEntity;

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
   * @ORM\ManyToOne(targetEntity="Tournament", inversedBy="competitions")
   * @var Tournament
   */
  protected $tournament;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $name;

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
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Competition constructor.
   */
  public function __construct()
  {
    $this->teams = new ArrayCollection();
    $this->phases = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
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
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getName(): string
  {
    $this->ensureNotNull('name');
    return $this->name;
  }

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
   * @param string $name
   * @return $this|Competition
   */
  public function setName(string $name): Competition
  {
    $this->name = $name;
    return $this;
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
}