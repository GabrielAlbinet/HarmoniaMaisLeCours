<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractWebTestCase extends WebTestCase
{

    const string URL_HOME = '/';
    const string URL_LOGIN = '/login';
    const string URL_FAVORITE = '/favoris';
    const string URL_ARTIST = '/artiste';
    const string URL_ALBUM = '/album';
    const string URL_PROFILE = '/profil';
    const string URL_REGISTER = '/register';

    protected function loginUser(
        KernelBrowser $client,
        string        $email
    ): void
    {
        $userRepo = $this->getRepository(UserRepository::class);
        $client->loginUser($userRepo->findOneBy(['email' => $email]));
    }

    protected function getRepository(string $repoClass): ServiceEntityRepository {
        return $this->getContainer()->get($repoClass);
    }

    protected function submitRegistration(
        KernelBrowser $client,
        string        $email,
        string        $password,
        string        $confirmation,
        bool          $agreeTerms
    ): void
    {
        $crawler = $client->request('GET', self::URL_REGISTER);
        $form = $crawler->selectButton("S'inscrire")->form();

        $form['registration_form[email]'] = $email;
        $form['registration_form[plainPassword][first]'] = $password;
        $form['registration_form[plainPassword][second]'] = $confirmation;

        if ($agreeTerms) {
            $form['registration_form[agreeTerms]']->tick();
        } else {
            $form['registration_form[agreeTerms]']->untick();
        }

        $client->submit($form);
    }
}