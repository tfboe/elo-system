<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 10:48 PM
 */

namespace App\Entity;


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

  /**
   * @ORM\Column(type="boolean", nullable=false)
   * @var bool
   */
  private $activated;

  /**
   * @return mixed
   */
  public function isActivated()
  {
    return $this->activated;
  }
//<editor-fold desc="Constructor">

  /**
   * RankingSystem constructor.
   */
  public function __construct()
  {
    $this->activated = false;
    $this->init();
  }
//</editor-fold desc="Constructor">
}