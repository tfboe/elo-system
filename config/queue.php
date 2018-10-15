<?php

return [
  'connections' => ['database' => [
    'retry_after' => env('QUEUE_RETRY_AFTER', 3660),
  ],
  ],
];
