<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/17/17
 * Time: 9:05 AM
 */

namespace App\Claims;

use Tymon\JWTAuth\Claims\Claim;

/**
 * Class Version
 * @package App\Claims
 */
class Version extends Claim
{
//<editor-fold desc="Fields">
  /**
   * {@inheritdoc}
   */
  protected $name = 'ver';
//</editor-fold desc="Fields">
}