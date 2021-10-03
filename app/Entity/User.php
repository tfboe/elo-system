<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 10:48 PM
 */

namespace App\Entity;

use App\Entity\Helpers\UserRights;
use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="elo_users")
 */
class User extends BaseEntity implements UserInterface
{
  use \Tfboe\FmLib\Entity\Traits\User;

//<editor-fold desc="Fields">
  /**
   * @ORM\Column(type="smallint", nullable=false)
   * @var int
   */
  private $rights;
//</editor-fold desc="Fields">

//<editor-fold desc="Constructor">
  /**
   * RankingSystem constructor.
   */
  public function __construct()
  {
    $this->rights = UserRights::UNACTIVATED;
    $this->init();
  }
//</editor-fold desc="Constructor">

//<editor-fold desc="Public Methods">
  /**
   * @return mixed
   */
  public function canRead()
  {
    return $this->rights >= UserRights::READ_ONLY;
  }

  public function canCreateTournaments()
  {
    return $this->rights >= UserRights::CREATE_TOURNAMENTS;
  }

  public function canManage()
  {
    return $this->rights >= UserRights::MANAGEMENT;
  }

  /**
   * @return bool
   */
  public function isAdmin(): bool
  {
    return $this->rights >= UserRights::ADMIN;
  }
//</editor-fold desc="Public Methods">
}