<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\Tfboe\FmLib\Entity\Ranking::class, function (/** @noinspection PhpUnusedParameterInspection */
  \Faker\Generator $faker, array $attributes) {
  return [
    'rank' => $attributes['rank'] !== null ? $attributes['rank'] : $attributes['uniqueRank'],
    'uniqueRank' => $attributes['uniqueRank'] !== null ? $attributes['uniqueRank'] : $attributes['rank'],
    'name' => ''
  ];
});