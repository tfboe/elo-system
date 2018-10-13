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
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\RecalculationInterface;

/**
 * Class LastRecalculation
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_recalculation")
 */
class Recalculation extends BaseEntity implements RecalculationInterface
{
  use \Tfboe\FmLib\Entity\Traits\Recalculation;
}