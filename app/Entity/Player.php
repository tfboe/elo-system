<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 10:27 AM
 */

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Tfboe\FmLib\Entity\Helpers\BaseEntity;
use Tfboe\FmLib\Entity\Helpers\NumericalId;
use Tfboe\FmLib\Entity\PlayerInterface;

/**
 * Class Player
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="elo_players",indexes={@ORM\Index(name="unique_names_birthday",
 *   columns={"first_name","last_name","birthday"})})
 */
class Player extends BaseEntity implements PlayerInterface
{
  use \Tfboe\FmLib\Entity\Traits\Player;
  use NumericalId;
//</editor-fold desc="Public Methods">
}