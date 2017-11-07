<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/18/17
 * Time: 10:22 PM
 */

namespace App\Entity\Categories;

use App\Helpers\BasicEnum;


/**
 * Class TeamMode
 * @package App\Entity\Categories
 */
abstract class TeamMode extends BasicEnum
{
//<editor-fold desc="Fields">
  const DOUBLE = 0;
  const DYP = 2;
  const SINGLE = 1;
//</editor-fold desc="Fields">
}