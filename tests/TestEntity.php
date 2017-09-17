<?php
declare(strict_types=1);

/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 1:44 AM
 */
class TestEntity extends \App\Entity\Helpers\BaseEntity
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
   * @return \App\Entity\User
   */
  public function getUser(): \App\Entity\User
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
   * @param \App\Entity\User $user
   */
  public function setUser(\App\Entity\User $user)
  {
    $this->user = $user;
  }
//</editor-fold desc="Public Methods">
}
