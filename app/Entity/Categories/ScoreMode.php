<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/18/17
 * Time: 10:22 PM
 */

namespace App\Entity\Categories;


/**
 * Class ScoreMode
 * @package App\Entity\Categories
 */
abstract class ScoreMode extends BasicEnum
{
//<editor-fold desc="Fields">
  const BEST_OF_FIVE = 2;
  const BEST_OF_THREE = 1;
  const ONE_SET = 0;
//</editor-fold desc="Fields">
}