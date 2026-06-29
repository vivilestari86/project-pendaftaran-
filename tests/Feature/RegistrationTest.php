<?php

namespace Tests\Feature;

use Tests\TestCase;

class RegistrationTest extends TestCase
{
    public function test_registration_form_has_profession_dropdown_and_empty_credentials(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSeeInOrder([
            'Administrasi Perkantoran',
            'Akuntansi',
            'BDRS',
            'Bidan D3',
            'Bidan D4/S1 + Profesi',
            'Dokter umum',
            'Ners',
            'Perawat D3',
            'Pranata Komputer IT',
            'Teknik Pendingin',
            'TTK (Asisten Apoteker)',
        ]);
        $response->assertDontSee('bidan D4/S1 + Profesi');
        $response->assertDontSee('Prakom IT');
        $response->assertDontSee('TTK (asisten APT)');
        $response->assertDontSee('Adminstrasi Perkantoran');

        $html = $response->getContent();

        $this->assertMatchesRegularExpression('/<select[^>]+name="profesi"[^>]*>/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+name="phone_number"[^>]+placeholder="08xx-xxxx-xxxx"/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+name="email"[^>]+value=""[^>]+autocomplete="off"/', $html);
        $this->assertMatchesRegularExpression('/<input[^>]+name="password"[^>]+value=""[^>]+autocomplete="new-password"/', $html);
    }

    public function test_registration_rejects_a_profession_outside_the_dropdown(): void
    {
        $response = $this->from(route('register'))->post(route('register.submit'), [
            'name' => 'User Baru',
            'profesi' => 'Profesi Lain',
            'email' => '',
            'phone_number' => '08123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms_agreed' => '1',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('profesi');
    }
}
