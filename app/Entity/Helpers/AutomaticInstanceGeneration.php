<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/2/18
 * Time: 8:53 PM
 */

namespace App\Entity\Helpers;


use App\Helpers\BasicEnum;

/**
 * Class AutomaticInstanceGeneration
 * @package App\Entity\Helpers
 */
class AutomaticInstanceGeneration extends BasicEnum
{
//<editor-fold desc="Fields">
  const MONTHLY = 1;
  const OFF = 0;
//</editor-fold desc="Fields">
}