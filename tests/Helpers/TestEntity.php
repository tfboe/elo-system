<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 1:44 AM
 */

namespace Tests\Helpers;

use App\Entity\Helpers\BaseEntity;
use App\Entity\User;

/**
 * Class TestEntity
 * @package Tests\Helpers
 */
class TestEntity extends BaseEntity
{
//<editor-fold desc="Fields">
  /** @var mixed $prop */
  public $prop = null;

  /** @var \App\Entity\User */
  public $user = null;
//</editor-fold desc="Fields">

//<editor-fold desc="Public Methods">
  /**
   * @return mixed
   */
  public function getProp()
  {
    return $this->prop;
  }

  /**
   * @return User
   */
  public function getUser(): User
  {
    return $this->user;
  }

  /**
   * @param mixed $prop
   */
  public function setProp($prop)
  {
    $this->prop = $prop;
  }

  /**
   * @param User $user
   */
  public function setUser(User $user)
  {
    $this->user = $user;
  }
//</editor-fold desc="Public Methods">
}
