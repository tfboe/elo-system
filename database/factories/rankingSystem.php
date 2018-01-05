<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\App\Entity\RankingSystem::class, function (\Faker\Generator $faker, array $attributes) {
  return [
    'defaultForLevel' => array_key_exists('defaultForLevel', $attributes) ? $attributes['defaultForLevel'] : null,
    'serviceName' => $attributes['serviceName'],
    'automaticInstanceGeneration' => array_key_exists('automaticInstanceGeneration', $attributes) ?
      $attributes['automaticInstanceGeneration'] : \App\Entity\Helpers\AutomaticInstanceGeneration::OFF,
    'subClassData' => [],
    'name' => $faker->name
  ];
});