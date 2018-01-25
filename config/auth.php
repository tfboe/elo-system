<?php
declare(strict_types=1);

return [
  'defaults' => [
    'guard' => env('AUTH_GUARD', 'api'),
    'passwords' => 'users',
  ],
  'guards' => [
    'api' => [
      'driver' => 'jwt-auth',
      'provider' => 'users'
    ],

    // ...
  ],

  'providers' => [
    'users' => [
      'driver' => 'doctrine',
      'model' => \Tfboe\FmLib\Entity\User::class
    ],
  ],
];