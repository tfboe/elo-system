<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\Tfboe\FmLib\Entity\Player::class, function (\Faker\Generator $faker, array $attributes) {
  return [
    'firstName' => array_key_exists('firstName', $attributes) ? $attributes['firstName'] : $faker->firstName,
    'lastName' => array_key_exists('lastName', $attributes) ? $attributes['lastName'] : $faker->lastName,
    'birthday' => new \DateTime($faker->date())
  ];
});