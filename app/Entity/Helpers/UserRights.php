<?php
declare(strict_types=1);

namespace App\Entity\Helpers;

use Tfboe\FmLib\Helpers\BasicEnum;


/**
 * Class GameMode
 * @package Tfboe\FmLib\Entity\Categories
 */
abstract class UserRights extends BasicEnum
{
//<editor-fold desc="Fields">
  const UNACTIVATED = 0;
  const READ_ONLY = 3;
  const CREATE_TOURNAMENTS = 6;
  const MANAGEMENT = 9;
  const ADMIN = 99;
//</editor-fold desc="Fields">
}