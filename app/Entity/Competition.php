<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/28/18
 * Time: 11:37 PM
 */

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\CompetitionInterface;
use Tfboe\FmLib\Entity\Helpers\TournamentHierarchyEntity;

/**
 * Class Competition
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_competitions",indexes={@ORM\Index(name="unique_name_idx", columns={"tournament_id","name"})})
 */
class Competition extends TournamentHierarchyEntity implements CompetitionInterface
{
  use \Tfboe\FmLib\Entity\Traits\Competition;

//<editor-fold desc="Constructor">

  /**
   * Competition constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->init();
  }
//</editor-fold desc="Constructor">
}