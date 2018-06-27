<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/28/18
 * Time: 11:38 PM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\TournamentInterface;

/**
 * Class Tournament
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_tournaments",indexes={@ORM\Index(name="user_id_idx", columns={"user_identifier","creator_id"})})
 */
class Tournament extends TournamentHierarchyEntity implements TournamentInterface
{
  use \Tfboe\FmLib\Entity\Traits\Tournament;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $userIdentifier;

  /**
   * @ORM\Column(type="boolean")
   * @var bool
   */
  private $finished;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">

  /**
   * Tournament constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->init();
  }

  /**
   * @return bool
   */
  public function isFinished(): bool
  {
    return $this->finished;
  }

  /**
   * @param bool $finished
   * @return $this|Tournament
   */
  public function setFinished(bool $finished): Tournament
  {
    $this->finished = $finished;
    return $this;
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return string
   */
  public function getUserIdentifier(): string
  {
    return $this->userIdentifier;
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