<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\App\Entity\Match::class, function (/** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'matchNumber' => $attributes['matchNumber'],
    'resultA' => $attributes['resultA'],
    'resultB' => $attributes['resultB'],
    'result' => $attributes['result'],
    'played' => $attributes['played'],
    'startTime' => $attributes['startTime'],
    'endTime' => $attributes['endTime']
  ];
});