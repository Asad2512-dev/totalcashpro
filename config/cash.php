<?php

declare(strict_types=1);

return [
    'default_opening_float' => (float) env('CASH_DEFAULT_OPENING_FLOAT', 100.00),
    'default_variance_threshold' => (float) env('CASH_DEFAULT_VARIANCE_THRESHOLD', 0.00),
    'currency' => env('CASH_DEFAULT_CURRENCY', 'GBP'),
];
