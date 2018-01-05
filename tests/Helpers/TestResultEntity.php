<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 1/3/18
 * Time: 12:08 PM
 */

namespace Tests\Helpers;


use App\Entity\Helpers\ResultEntity;
use App\Entity\Helpers\UnsetProperty;

/**
 * Class TestResultEntity
 * @package Tests\Helpers
 */
class TestResultEntity
{
  use ResultEntity;
  use UnsetProperty;
}