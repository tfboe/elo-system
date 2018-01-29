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
use Tfboe\FmLib\Entity\GameInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;

/**
 * Class Game
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="games")
 */
class Game extends TournamentHierarchyEntity implements GameInterface
{
  use \Tfboe\FmLib\Entity\Traits\Game;

//<editor-fold desc="Constructor">

  /**
   * Game constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->init();
  }
//</editor-fold desc="Constructor">
}