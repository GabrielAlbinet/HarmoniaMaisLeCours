<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\AbstractWebTestCase;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\TestWith;

class RegistrationControllerTest extends AbstractWebTestCase
{
    /** BLOC DE TEST 1 POUR LA VUE DE L'INSCRIPTION */

    public function testFormExists(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL_REGISTER);

        $this->assertSelectorExists('input[name="registration_form[email]"]');
        $this->assertSelectorExists('input[name="registration_form[plainPassword][first]"]');
        $this->assertSelectorExists('input[name="registration_form[plainPassword][second]"]');
        $this->assertSelectorExists('input[name="registration_form[agreeTerms]"]');
        $this->assertSelectorTextContains('button[type="submit"]', "S'inscrire");
    }

    public function testAccessOK(): void
    {
        $client = static::createClient();
        $client->request('GET', self::URL_REGISTER);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription');
    }

    /** BLOC DE TEST 2 POUR LA VALIDITÉ DE L'INSCRIPTION */

    public function testRegisterOK(): void
    {
        $client = static::createClient();
        $email = 'inscription.' . uniqid() . '@example.com';

        $this->submitRegistration($client, $email, 'secret123', 'secret123', true);

        $this->assertResponseRedirects(self::URL_HOME);

        $user = $this->getRepository(UserRepository::class)->findOneBy(['email' => $email]);
        $this->assertNotNull($user);
        $this->assertNotSame('secret123', $user->getPassword());

        $entityManager = $this->getContainer()->get(EntityManagerInterface::class);
        $entityManager->remove($user);
        $entityManager->flush();
    }

    #[TestWith(['nouveau@example.com', 'secret123', 'autre', true, 'Les deux champs doivent être identiques.'])]
    #[TestWith(['nouveau@example.com', 'secret123', 'secret123', false, 'You should agree to our terms.'])]
    #[TestWith(['kelly.cassin@example.com', 'secret123', 'secret123', true, 'There is already an account with this email'])]

    /** BLOC DE TEST 3 POUR LES INSCRIPTIONS REFUSÉES */

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