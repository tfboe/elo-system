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
use Tfboe\FmLib\Entity\TeamMembershipInterface;

/**
 * Class Team
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="teamMemberships")
 */
class TeamMembership extends BaseEntity implements TeamMembershipInterface
{
  use \Tfboe\FmLib\Entity\Traits\TeamMembership;
}