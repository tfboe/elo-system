<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/22/17
 * Time: 6:28 PM
 */

namespace App\Entity\Categories;


/**
 * Class Table
 * @package App\Entity\Categories
 */
class Table extends BasicEnum
{
  //<editor-fold desc="Fields">
  const BONZINI = 5; // only used if real game table is unknown / forgotten in multi table tournament
  const GARLANDO = 1;
  const LEONHART = 2;
  const MULTITABLE = 0;
  const ROBERTO_SPORT = 4;
  const TORNADO = 3;
//</editor-fold desc="Fields">
}