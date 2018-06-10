<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/15/17
 * Time: 10:48 PM
 */

namespace App\Entity;


use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseEntity implements UserInterface
{
  use \Tfboe\FmLib\Entity\Traits\User;

//<editor-fold desc="Constructor">

  /**
   * RankingSystem constructor.
   */
  public function __construct()
  {
    $this->init();
  }
//</editor-fold desc="Constructor">
}