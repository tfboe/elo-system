<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/28/18
 * Time: 11:38 PM
 */

namespace App\Entity;

use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\TournamentInterface;

/**
 * Class Tournament
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="tournaments",indexes={@ORM\Index(name="user_id_idx", columns={"user_identifier","creator_id"})})
 */
class Tournament extends TournamentHierarchyEntity implements TournamentInterface
{
  use \Tfboe\FmLib\Entity\Traits\Tournament;

  /**
   * @ORM\Column(type="string")
   * @var string
   */
  private $userIdentifier;

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
//</editor-fold desc="Constructor">
}