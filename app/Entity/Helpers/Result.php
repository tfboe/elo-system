<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 12/16/17
 * Time: 1:06 PM
 */

namespace App\Entity\Helpers;

use App\Helpers\BasicEnum;

/**
 * Class Result
 * @package App\Entity\Helpers
 */
abstract class Result extends BasicEnum
{
//<editor-fold desc="Fields">
  const DRAW = 2;
  const NOT_YET_FINISHED = 4;
  const NULLED = 3;
  const TEAM_A_WINS = 0;
  const TEAM_B_WINS = 1;
//</editor-fold desc="Fields">
}