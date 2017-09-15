<?php

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
      'model' => \App\Entity\User::class
    ],
  ],
];