<?php
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 9/16/17
 * Time: 11:48 AM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\App\Entity\User::class, function (\Faker\Generator $faker, array $attributes) {
  if (array_key_exists('unhashedPassword', $attributes)) {
    $password = $attributes['unhashedPassword'];
  } else {
    $password = $faker->password(8, 30);
  }
  return [
    'password' => Hash::make($password),
    'email' => $faker->email
  ];
});