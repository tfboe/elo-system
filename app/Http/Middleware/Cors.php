<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 6/10/18
 * Time: 5:31 PM
 */

namespace App\Http\Middleware;


use palanik\lumen\Middleware\LumenCors;

/**
 * Class Cors
 * @package App\Http\Middleware
 */
class Cors extends LumenCors
{
//<editor-fold desc="Fields">
  protected $settings = [
    'origin' => '*',
    'allowMethods' => 'GET,POST,OPTIONS',
    'exposeHeaders' => 'jwt-token'
  ];
//</editor-fold desc="Fields">
}