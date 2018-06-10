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
use Tfboe\FmLib\Entity\RankingInterface;

/**
 * Class Ranking
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="rankings")
 *
 * Method hint for getName, since it will never throw an exception (name gets initialized empty)
 * @method string getName()
 */
class Ranking extends BaseEntity implements RankingInterface
{
  use \Tfboe\FmLib\Entity\Traits\Ranking;


//<editor-fold desc="Constructor">

  /**
   * Ranking constructor.
   */
  public function __construct()
  {
    $this->init();
  }
//</editor-fold desc="Constructor">
}