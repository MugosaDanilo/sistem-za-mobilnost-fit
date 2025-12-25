<?php

use App\Models\Fakultet;

it('castuje uputstvo_za_ocjene u string', function () {
    $fakultet = new Fakultet([
        'uputstvo_za_ocjene' => 12345,
    ]);

    expect($fakultet->uputstvo_za_ocjene)->toBeString();
});

it('dozvoljava prazan string za uputstvo_za_ocjene', function () {
    $fakultet = new Fakultet([
        'uputstvo_za_ocjene' => '',
    ]);

    expect($fakultet->uputstvo_za_ocjene)->toBeString();
});

it('provjerava da uputstvo_za_ocjene postoji kao help tooltip podatak', function () {
    $fakultet = new Fakultet([
        'uputstvo_za_ocjene' => 'Pomoć za ocjenjivanje',
    ]);

    expect($fakultet->uputstvo_za_ocjene)->not->toBeNull();
});
