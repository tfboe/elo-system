<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\Tfboe\FmLib\Entity\RankingSystem::class, function (\Faker\Generator $faker, array $attributes) {
  return [
    'defaultForLevel' => array_key_exists('defaultForLevel', $attributes) ? $attributes['defaultForLevel'] : null,
    'serviceName' => $attributes['serviceName'],
    'generationInterval' => array_key_exists('generationInterval', $attributes) ?
      $attributes['generationInterval'] : \Tfboe\FmLib\Entity\Helpers\AutomaticInstanceGeneration::OFF,
    'subClassData' => [],
    'name' => $faker->name
  ];
});