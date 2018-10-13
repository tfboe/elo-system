<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/13/18
 * Time: 3:32 AM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\TournamentHierarchyEntityRankingTimeInterface;

/**
 * Class Team
 * @package Tfboe\FmLib\Entity
 * @ORM\Entity
 * @ORM\Table(name="tournamentHierarchyEntityRankingTimes")
 */
class TournamentHierarchyEntityRankingTime extends BaseEntity implements TournamentHierarchyEntityRankingTimeInterface
{
  use \Tfboe\FmLib\Entity\Traits\TournamentHierarchyEntityRankingTime;
}