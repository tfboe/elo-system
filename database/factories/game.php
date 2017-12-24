<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\App\Entity\Game::class, function (/** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'gameNumber' => $attributes['gameNumber'],
    'resultA' => $attributes['resultA'],
    'resultB' => $attributes['resultB'],
    'result' => $attributes['result'],
    'played' => $attributes['played'],
    'startTime' => $attributes['startTime'],
    'endTime' => $attributes['endTime']
  ];
});