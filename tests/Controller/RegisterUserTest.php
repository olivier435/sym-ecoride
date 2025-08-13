<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form[name="registration_form"]');

        $randomSuffix = uniqid();

        $client->submitForm("S'enregistrer", [
            'registration_form[pseudo]' => 'testuser_' . $randomSuffix,
            'registration_form[email]' => 'testuser_' . $randomSuffix . '@example.com',
            'registration_form[plainPassword]' => '%&dMpD5#K#<btSG1SCQrBgpL',
            'registration_form[firstname]' => 'Test',
            'registration_form[lastname]' => 'User',
            'registration_form[phone]' => '0612345678',
            'registration_form[adress]' => '12 rue de la Victoire',
            'registration_form[postalCode]' => '75001',
            'registration_form[city]' => 'Paris',
            'registration_form[agreeTerms]' => true,
        ]);

        $this->assertResponseRedirects('/login/success');
        $client->followRedirect();

        // Facultatif
        $this->assertSelectorTextContains('body', 'Redirecting');
    }

    public function testRegistrationFormValidationErrors(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/inscription');

        $crawler = $client->submitForm("S'enregistrer", [
            'registration_form[pseudo]' => '',
            'registration_form[email]' => 'not-an-email',
            'registration_form[plainPassword]' => 'abc',
            'registration_form[firstname]' => '',
            'registration_form[lastname]' => '',
            'registration_form[phone]' => '',
            'registration_form[adress]' => '',
            'registration_form[postalCode]' => '123',
            'registration_form[city]' => '',
            'registration_form[agreeTerms]' => false,
        ]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorExists('.is-invalid');

        // Vérification par champ
        $this->assertSelectorTextContains('#registration_form_pseudo + .invalid-feedback', 'Merci d\'indiquer votre pseudo');
        $this->assertSelectorTextContains('#registration_form_email + .invalid-feedback', 'adresse e-mail');
        $this->assertSelectorTextContains('#registration_form_postalCode + .invalid-feedback', 'code postal');
        $this->assertSelectorTextContains('#registration_form_city + .invalid-feedback', 'ville');
    }
}