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
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;
use Tfboe\FmLib\Entity\MatchInterface;

/**
 * Class Match
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="matches")
 */
class Match extends TournamentHierarchyEntity implements MatchInterface
{
  use \Tfboe\FmLib\Entity\Traits\Match;

//<editor-fold desc="Constructor">

  /**
   * Match constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->init();
  }
//</editor-fold desc="Constructor">
}