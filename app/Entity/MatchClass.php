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
 * Class MatchClass
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_matches")
 */
class MatchClass extends TournamentHierarchyEntity implements MatchInterface
{
  use \Tfboe\FmLib\Entity\Traits\MatchClass;

//<editor-fold desc="Constructor">

  /**
   * MatchClass constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->init();
  }
//</editor-fold desc="Constructor">
}