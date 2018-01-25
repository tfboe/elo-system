<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: benedikt
 * Date: 10/1/17
 * Time: 5:44 PM
 */

/** @var \LaravelDoctrine\ORM\Testing\Factory $factory */

$factory->define(\Tfboe\FmLib\Entity\Tournament::class, function (\Faker\Generator $faker, array $attributes) {
  return [
    'name' => $faker->city . " Tournament",
    'userIdentifier' => $attributes['userIdentifier'],
    'creator' => $attributes['creator'],
    'tournamentListId' => array_key_exists('tournamentListId', $attributes) ? $attributes['tournamentListId'] : '',
    'gameMode' => array_key_exists('gameMode', $attributes) ? $attributes['gameMode'] : null,
    'organizingMode' => array_key_exists('organizingMode', $attributes) ? $attributes['organizingMode'] : null,
    'scoreMode' => array_key_exists('scoreMode', $attributes) ? $attributes['scoreMode'] : null,
    'teamMode' => array_key_exists('teamMode', $attributes) ? $attributes['teamMode'] : null,
    'table' => array_key_exists('table', $attributes) ? $attributes['table'] : null,
    'competitions' => new \Doctrine\Common\Collections\ArrayCollection(),
    'startTime' => array_key_exists('startTime', $attributes) ? $attributes['startTime'] : null,
    'endTime' => array_key_exists('endTime', $attributes) ? $attributes['endTime'] : null
  ];
});