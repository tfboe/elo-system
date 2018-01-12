<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 11:35 AM
 */

namespace App\Entity;


use App\Entity\Helpers\NameEntity;
use App\Entity\Helpers\TimestampableEntity;
use App\Entity\Helpers\TournamentHierarchyEntity;
use App\Entity\Helpers\TreeStructureEntityInterface;
use App\Helpers\Level;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tournament
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="tournaments",indexes={@ORM\Index(name="user_id_idx", columns={"user_identifier","creator_id"})})
 */
class Tournament extends TournamentHierarchyEntity implements TreeStructureEntityInterface
{
  use TimestampableEntity;
  use NameEntity;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $userIdentifier;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $tournamentListId;
  /**
   * @ORM\ManyToOne(targetEntity="User")
   * @var User
   */
  protected $creator;
  /**
   * @ORM\OneToMany(targetEntity="Competition", mappedBy="tournament",indexBy="name")
   * @var Collection|Competition[]
   */
  protected $competitions;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * Tournament constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->tournamentListId = "";
    $this->competitions = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @inheritDoc
   */
  public function getChildren(): Collection
  {
    return $this->getCompetitions();
  }

  /**
   * @return Competition[]|Collection
   */
  public function getCompetitions()
  {
    return $this->competitions;
  }

  /**
   * @return User
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getCreator(): User
  {
    $this->ensureNotNull('creator');
    return $this->creator;
  }

  /**
   * @inheritDoc
   */
  public function getLevel(): int
  {
    return Level::TOURNAMENT;
  }

  /**
   * @inheritDoc
   */
  public function getLocalIdentifier()
  {
    return $this->getId();
  }

  /**
   * @inheritDoc
   */
  public function getParent(): ?TreeStructureEntityInterface
  {
    return null;
  }

  /**
   * @return string
   */
  public function getTournamentListId(): string
  {
    return $this->tournamentListId;
  }

  /**
   * @return string
   * @throws \App\Exceptions\ValueNotSet
   */
  public function getUserIdentifier(): string
  {
    $this->ensureNotNull('userIdentifier');
    return $this->userIdentifier;
  }

  /**
   * @param User $creator
   * @return $this|Tournament
   */
  public function setCreator(User $creator): Tournament
  {
    $this->creator = $creator;
    return $this;
  }

  /**
   * @param string $tournamentListId
   * @return $this|Tournament
   */
  public function setTournamentListId(string $tournamentListId): Tournament
  {
    $this->tournamentListId = $tournamentListId;
    return $this;
  }

  /**
   * @param string $userIdentifier
   * @return $this|Tournament
   */
  public function setUserIdentifier(string $userIdentifier): Tournament
  {
    $this->userIdentifier = $userIdentifier;
    return $this;
  }
//</editor-fold desc="Public Methods">
}