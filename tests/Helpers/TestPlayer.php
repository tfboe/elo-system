<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/24/17
 * Time: 9:13 AM
 */

namespace Tests\Helpers;


use App\Entity\Player;

/**
 * Class TestPlayer
 * @package Tests\Helpers
 */
class TestPlayer extends Player
{
//<editor-fold desc="Constructor">
  function __construct()
  {
    $this->id = 0;
  }
//</editor-fold desc="Constructor">
}