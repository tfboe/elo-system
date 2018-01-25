<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 11:48 AM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\Tfboe\FmLib\Entity\User::class, function (\Faker\Generator $faker, array $attributes) {
  if (array_key_exists('originalPassword', $attributes)) {
    $password = $attributes['originalPassword'];
  } else {
    $password = $faker->password(8, 30);
  }
  return [
    'password' => \Illuminate\Support\Facades\Hash::make($password),
    'email' => $faker->email,
    'jwtVersion' => 1,
    'confirmedAGBVersion' => 0,
  ];
});