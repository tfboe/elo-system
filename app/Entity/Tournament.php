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
use Tfboe\FmLib\Entity\TournamentInterface;

/**
 * Class Tournament
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="tournaments",indexes={@ORM\Index(name="user_id_idx", columns={"user_identifier","creator_id"})})
 */
class Tournament extends TournamentHierarchyEntity implements TournamentInterface
{
  use \Tfboe\FmLib\Entity\Traits\Tournament;

//<editor-fold desc="Constructor">

  /**
   * Tournament constructor.
   */
  public function __construct()
  {
    parent::__construct();
    $this->init();
  }
//</editor-fold desc="Constructor">
}