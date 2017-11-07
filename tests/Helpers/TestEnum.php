<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 11:41 AM
 */

namespace Tests\Helpers;


use App\Helpers\BasicEnum;

/**
 * Class TestEnum
 * @package Tests\Helpers
 */
abstract class TestEnum extends BasicEnum
{
//<editor-fold desc="Fields">
  const INT_KEY = 1;
  const KEY = "value";
//</editor-fold desc="Fields">
}