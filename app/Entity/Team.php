<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/20/17
 * Time: 12:30 PM
 */

namespace App\Entity;

use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\TeamInterface;

/**
 * Class Team
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="teams")
 */
class Team extends BaseEntity implements TeamInterface
{
  use \Tfboe\FmLib\Entity\Traits\Team;

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