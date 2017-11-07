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
 * Class OrganizingMode
 * @package App\Entity\Categories
 */
abstract class OrganizingMode extends BasicEnum
{
//<editor-fold desc="Fields">
  const ELIMINATION = 0;
  const QUALIFICATION = 1;
//</editor-fold desc="Fields">
}