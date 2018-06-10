<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/5/18
 * Time: 10:54 PM
 */

namespace App\Entity;


use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\RankingSystemListInterface;

/**
 * Class RankingSystemList
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankingSystemLists")
 */
class RankingSystemList extends BaseEntity implements RankingSystemListInterface
{
  use \Tfboe\FmLib\Entity\Traits\RankingSystemList;

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