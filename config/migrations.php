<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Entity Manager Migrations Configuration
  |--------------------------------------------------------------------------
  |
  | Each entity manager can have a custom migration configuration. Provide
  | the name of the entity manager as the key, then duplicate the settings.
  | This will allow generating custom migrations per EM instance and not have
  | collisions when executing them.
  |
  */
  'default' => [
    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | Tables which are filtered by Regular Expression. You optionally
    | exclude or limit to certain tables. The default will
    | filter all tables.
    |
    */
    'schema' => [
      'filter' => '/^(?!password_resets|jobs|failed_jobs).*$/'
    ]
  ],
];
