<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 11:35 AM
 */

namespace App\Entity;

use App\Entity\CategoryTraits\GameMode;
use App\Entity\CategoryTraits\OrganizingMode;
use App\Entity\CategoryTraits\ScoreMode;
use App\Entity\CategoryTraits\Table;
use App\Entity\CategoryTraits\TeamMode;
use App\Entity\Helpers\BaseEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tournament
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="tournaments",indexes={@ORM\Index(name="user_id_idx", columns={"user_identifier","creator_id"})})
 */
class Tournament extends BaseEntity
{
  use GameMode;
  use TeamMode;
  use OrganizingMode;
  use ScoreMode;
  use Table;

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
   * @ORM\Column(type="string")
   * @var string
   */
  protected $userIdentifier;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  protected $name;

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
    $this->tournamentListId = "";
    $this->competitions = new ArrayCollection();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return Competition[]|Collection
   */
  public function getCompetitions()
  {
    return $this->competitions;
  }

  /**
   * @return User
   */
  public function getCreator(): User
  {
    $this->ensureNotNull('creator');
    return $this->creator;
  }

  /**
   * @return string
   */
  public function getId(): string
  {
    $this->ensureNotNull('id');
    return $this->id;
  }

  /**
   * @return string
   */
  public function getName(): string
  {
    $this->ensureNotNull('name');
    return $this->name;
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
   * @param string $name
   * @return $this|Tournament
   */
  public function setName(string $name): Tournament
  {
    $this->name = $name;
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
   * @param string $user_identifier
   * @return $this|Tournament
   */
  public function setUserIdentifier(string $user_identifier): Tournament
  {
    $this->userIdentifier = $user_identifier;
    return $this;
  }
//</editor-fold desc="Public Methods">
}