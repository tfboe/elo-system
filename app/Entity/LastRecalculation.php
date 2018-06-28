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
use Tfboe\FmLib\Entity\LastRecalculationInterface;

/**
 * Class LastRecalculation
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_lastRecalculation")
 */
class LastRecalculation extends BaseEntity implements LastRecalculationInterface
{
  use \Tfboe\FmLib\Entity\Traits\LastRecalculation;
}