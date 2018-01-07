<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 8:47 PM
 */

namespace App\Entity;

use App\Entity\Helpers\AutomaticInstanceGeneration;
use App\Entity\Helpers\BaseEntity;
use App\Entity\Helpers\NameEntity;
use App\Entity\Helpers\SubClassData;
use App\Entity\Helpers\TimestampableEntity;
use App\Entity\Helpers\UUIDEntity;
use App\Exceptions\ValueNotValid;
use App\Helpers\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class RankingSystem
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystems")
 */
class RankingSystem extends BaseEntity
{
  use SubClassData;
  use TimestampableEntity;
  use UUIDEntity;
  use NameEntity;

  //<editor-fold desc="Fields">

  /**
   * @ORM\Column(type="string")
   *
   * @var string
   */
  protected $serviceName;
  /**
   * @ORM\Column(type="smallint", nullable=true)
   * @var int|null
   */
  protected $defaultForLevel;
  /**
   * @ORM\Column(type="integer")
   * @var int
   */
  private $automaticInstanceGeneration;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="Tournament",
   *     mappedBy="rankingSystems",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__tournament_ranking_systems")
   * @var Collection|Tournament[]
   */
  private $tournaments;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="Competition",
   *     mappedBy="rankingSystems",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__competition_ranking_systems")
   * @var Collection|Competition[]
   */
  private $competitions;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="Phase",
   *     mappedBy="rankingSystems",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__phase_ranking_systems")
   * @var Collection|Phase[]
   */
  private $phases;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="Match",
   *     mappedBy="rankingSystems",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__match_ranking_systems")
   * @var Collection|Match[]
   */
  private $matches;

  /**
   * @ORM\ManyToMany(
   *     targetEntity="Game",
   *     mappedBy="rankingSystems",
   *     indexBy="id"
   * )
   * @ORM\JoinTable(name="relation__game_ranking_systems")
   * @var Collection|Game[]
   */
  private $games;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   * @var \DateTime|null
   */
  private $openSyncFrom;

  /**
   * @ORM\OneToMany(targetEntity="RankingSystemList", mappedBy="rankingSystem", indexBy="id")
   * @var Collection|RankingSystemList[]
   */
  private $lists;

  /**
   * @return RankingSystemList[]|Collection
   */
  public function getLists(): Collection
  {
    return $this->lists;
  }

  /**
   * @return \DateTime|null
   */
  public function getOpenSyncFrom(): ?\DateTime
  {
    return $this->openSyncFrom;
  }

  /**
   * @param \DateTime|null $openSyncFrom
   * @return $this|RankingSystem
   */
  public function setOpenSyncFrom(?\DateTime $openSyncFrom): RankingSystem
  {
    $this->openSyncFrom = $openSyncFrom;
    return $this;
  }
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * TournamentRankingSystem constructor.
   * @param string[] $keys the keys of the subclass properties
   */
  public function __construct(array $keys)
  {
    $this->initSubClassData($keys);
    $this->automaticInstanceGeneration = AutomaticInstanceGeneration::OFF;
    $this->defaultForLevel = null;
    $this->tournaments = new ArrayCollection();
    $this->competitions = new ArrayCollection();
    $this->phases = new ArrayCollection();
    $this->matches = new ArrayCollection();
    $this->games = new ArrayCollection();
    $this->openSyncFrom = null;
    $this->lists = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return int
   */
  public function getAutomaticInstanceGeneration(): int
  {
    return $this->automaticInstanceGeneration;
  }

  /**
   * @return Competition[]|Collection
   */
  public function getCompetitions(): Collection
  {
    return $this->competitions;
  }

  /**
   * @return int|null
   */
  public function getDefaultForLevel(): ?int
  {
    return $this->defaultForLevel;
  }

  /**
   * @return Game[]|Collection
   */
  public function getGames(): Collection
  {
    return $this->games;
  }

  /**
   * @return Match[]|Collection
   */
  public function getMatches(): Collection
  {
    return $this->matches;
  }

  /**
   * @return Phase[]|Collection
   */
  public function getPhases(): Collection
  {
    return $this->phases;
  }

  /**
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getServiceName(): string
  {
    $this->ensureNotNull('serviceName');
    return $this->serviceName;
  }

  /**
   * @return Collection|Tournament[]
   */
  public function getTournaments(): Collection
  {
    return $this->tournaments;
  }

  /**
   * @param int $automaticInstanceGeneration
   * @return $this|RankingSystem
   * @throws \App\Exceptions\ValueNotValid
   */
  public function setAutomaticInstanceGeneration(int $automaticInstanceGeneration): RankingSystem
  {
    AutomaticInstanceGeneration::ensureValidValue($automaticInstanceGeneration);
    $this->automaticInstanceGeneration = $automaticInstanceGeneration;
    return $this;
  }

  /**
   * @param int|null $defaultForLevel
   * @return $this|RankingSystem
   * @throws ValueNotValid
   */
  public function setDefaultForLevel(?int $defaultForLevel): RankingSystem
  {
    if ($defaultForLevel !== null) {
      Level::ensureValidValue($defaultForLevel);
    }
    $this->defaultForLevel = $defaultForLevel;
    return $this;
  }

  /**
   * @param string $serviceName
   * @return $this|RankingSystem
   */
  public function setServiceName(string $serviceName): RankingSystem
  {
    $this->serviceName = $serviceName;
    return $this;
  }
//</editor-fold desc="Public Methods">
}