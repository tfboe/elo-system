<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/3/17
 * Time: 5:55 PM
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\QualificationSystemInterface;

/**
 * Class QualificationSystem
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_qualificationSystems")
 */
class QualificationSystem extends BaseEntity implements QualificationSystemInterface
{
  use \Tfboe\FmLib\Entity\Traits\QualificationSystem;
}