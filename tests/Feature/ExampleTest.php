<?php

it('returns a successful response', function () {
    $response = $this->get('/');

    // Pošto neulogovanog šalje na login:
    $response->assertStatus(302);
    $response->assertRedirect(route('login'));
});
