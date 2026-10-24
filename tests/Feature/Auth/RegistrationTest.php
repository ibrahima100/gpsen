<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);

    $responseInscription = $this->get('/inscription');
    $responseInscription->assertStatus(200);
});

test('new users can register as expediteur', function () {
    $response = $this->post('/inscription/expediteur', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'phone' => '+221 77 000 00 00',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
