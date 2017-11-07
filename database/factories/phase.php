<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */
$factory->define(\App\Entity\Phase::class, function (/** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'name' => '',
    'phaseNumber' => $attributes['phaseNumber'],
  ];
});