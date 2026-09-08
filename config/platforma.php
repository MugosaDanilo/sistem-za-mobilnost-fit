<?php

/*
|--------------------------------------------------------------------------
| Integracija sa studentskom platformom (platforma-united)
|--------------------------------------------------------------------------
| Platforma je izvor istine za studente i matične predmete.
| Token se izdaje na platformi: php artisan integracija:token
*/
return [
    'url' => rtrim((string) env('PLATFORMA_URL', ''), '/'),
    'token' => env('PLATFORMA_TOKEN'),
    'timeout' => (int) env('PLATFORMA_TIMEOUT', 10),
    'cache_ttl' => (int) env('PLATFORMA_CACHE_TTL', 60),

    // ID matičnog fakulteta na platformi (FIT = 5)
    'maticni_fakultet_id' => (int) env('PLATFORMA_MATICNI_FAKULTET_ID', 5),

    // Slanje priznatih ispita nazad u platformu (student_pfs)
    'writeback' => filter_var(env('PLATFORMA_WRITEBACK_ENABLED', false), FILTER_VALIDATE_BOOL),

    'enabled' => (bool) env('PLATFORMA_URL') && (bool) env('PLATFORMA_TOKEN'),
];
