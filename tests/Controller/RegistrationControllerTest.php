<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\AbstractWebTestCase;
use PHPUnit\Framework\Attributes\TestWith;

class RegistrationControllerTest extends AbstractWebTestCase
{
    /* BLOC 1 de Tests : Affichage et vue globale */

    public function testAccessOK(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL_REGISTER);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    #[TestWith(['input[name="registration_form[email]"]'])]
    #[TestWith(['input[name="registration_form[plainPassword][first]"][type="password"]'])]
    #[TestWith(['input[name="registration_form[plainPassword][second]"][type="password"]'])]
    #[TestWith(['input[name="registration_form[agreeTerms]"][type="checkbox"]'])]
    #[TestWith(['button[type="submit"]'])]
    public function testFormFieldExists(string $selector): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL_REGISTER);

        $this->assertSelectorExists($selector);
    }

    #[TestWith(['Email'])]
    #[TestWith(['Accepter les termes et conditions'])]
    #[TestWith(["S'inscrire"])]
    public function testFormLabels(string $text): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL_REGISTER);

        $this->assertSelectorTextContains('form', $text);
    }

    public function testFormIsEmptyOnFirstDisplay(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL_REGISTER);

        $this->assertInputValueSame('registration_form[email]', '');
        $this->assertCheckboxNotChecked('registration_form[agreeTerms]');
    }


    /* BLOC 2 de Tests : l'inscription passe la douane */

    public function testRegisterOK(): void
    {
        $client = static::createClient();
        $email = 'inscription.' . uniqid() . '@example.com';

        $this->submitRegistration($client, $email, 'secret123', 'secret123', true);

        $this->assertResponseRedirects(self::URL_HOME);

        $user = $this->getRepository(UserRepository::class)->findOneBy(['email' => $email]);
        $this->assertNotNull($user);
        $this->assertNotSame('secret123', $user->getPassword());

        $this->removeEntity($user);
    }

    #[TestWith(['classique', 'secret123'])]
    public function testRegisterRedirectsToHome(string $prefix, string $password): void
    {
        $client = static::createClient();
        $email = $prefix . '.' . uniqid() . '@example.com';

        $this->submitRegistration($client, $email, $password, $password, true);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects(self::URL_HOME);

        $client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Les dernières sorties');

        $user = $this->getRepository(UserRepository::class)->findOneBy(['email' => $email]);
        $this->removeEntity($user);
    }


    /* BLOC 3 de Tests : l'inscription foire */

    #[TestWith(['nouveau@example.com', 'secret123', 'autre', true, 'Les deux champs doivent être identiques.'])]
    #[TestWith(['nouveau@example.com', 'secret123', 'secret123', false, 'You should agree to our terms.'])]
    #[TestWith(['kelly.cassin@example.com', 'secret123', 'secret123', true, 'There is already an account with this email'])]
    #[TestWith(['pas-un-email', 'secret123', 'secret123', true, 'This value is not a valid email address.'])]
    #[TestWith(['nouveau@example.com', 'court', 'court', true, 'Le mot de passe doit contenir au moins 6 caractères.'])]
    public function testRegisterKO(
        string $email,
        string $password,
        string $confirmation,
        bool   $agreeTerms,
        string $message
    ): void
    {
        $client = static::createClient();

        $this->submitRegistration($client, $email, $password, $confirmation, $agreeTerms);

        $this->assertResponseStatusCodeSame(422);
        $this->assertSelectorTextContains('form', $message);
    }
}