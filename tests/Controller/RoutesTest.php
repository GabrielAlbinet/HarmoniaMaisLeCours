<?php

namespace App\Tests;

use App\Repository\UserRepository;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoutesTest extends WebTestCase
{
    #[DataProvider('providePublicUrls')]
    public function testPublicUrlIsSuccessful(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    #[TestWith(['/profil'])]
    #[TestWith(['/favoris'])]
    public function testProtectedUrlRedirectsToLogin(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects('/login');
    }

    #[TestWith(['/profil'])]
    #[TestWith(['/favoris'])]
    public function testProtectedUrlIsSuccessfulWhenLoggedIn(string $url): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser('kelly.cassin@example.com'));
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    public function testUnknownAlbumRedirectsToHome(): void
    {
        $client = static::createClient();
        $client->request('GET', '/album/999999');

        $this->assertResponseRedirects('/');
    }

    #[DataProvider('provideAdminUrls')]
    public function testAdminUrlRedirectsToLoginWhenAnonymous(string $url): void
    {
        $client = static::createClient();
        $client->request('GET', $url);

        $this->assertResponseRedirects('/login');
    }

    #[DataProvider('provideAdminUrls')]
    public function testAdminUrlIsForbiddenForSimpleUser(string $url): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser('kelly.cassin@example.com'));
        $client->request('GET', $url);

        $this->assertResponseStatusCodeSame(403);
    }

    #[DataProvider('provideAdminUrls')]
    public function testAdminUrlIsSuccessfulForAdmin(string $url): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser('admin@admin.fr'));
        $client->request('GET', $url);

        $this->assertResponseIsSuccessful();
    }

    #[DataProvider('provideRouteNames')]
    public function testRouteNameGeneratesExpectedPath(string $name, array $parameters, string $expected): void
    {
        static::bootKernel();

        $this->assertSame($expected, static::getContainer()->get('router')->generate($name, $parameters));
    }

    public static function providePublicUrls(): array
    {
        return [
            'accueil' => ['/'],
            'connexion' => ['/login'],
            'inscription' => ['/register'],
            'artistes' => ['/artiste'],
            'artiste Woodkid' => ['/artiste/39'],
            'artiste Durgan Wendy' => ['/artiste/58'],
            'artiste Buckridge Willie' => ['/artiste/70'],
            'album 109' => ['/album/109'],
            'album 135' => ['/album/135'],
            'album 206' => ['/album/206'],
            'album sans morceau' => ['/album/218'],
        ];
    }

    public static function provideAdminUrls(): array
    {
        return [
            'tableau de bord' => ['/admin'],
            'albums' => ['/admin/album'],
            'artistes' => ['/admin/artiste'],
        ];
    }

    public static function provideRouteNames(): array
    {
        return [
            'app_home' => ['app_home', [], '/'],
            'app_login' => ['app_login', [], '/login'],
            'app_logout' => ['app_logout', [], '/logout'],
            'app_register' => ['app_register', [], '/register'],
            'app_profil' => ['app_profil', [], '/profil'],
            'app_favorite_index' => ['app_favorite_index', [], '/favoris'],
            'app_artist_index' => ['app_artist_index', [], '/artiste'],
            'app_artist_show' => ['app_artist_show', ['id' => 39], '/artiste/39'],
            'app_album_item' => ['app_album_item', ['id' => 109], '/album/109'],
            'admin_dashboard' => ['admin_dashboard', [], '/admin'],
            'admin_album_index' => ['admin_album_index', [], '/admin/album'],
            'admin_artist_index' => ['admin_artist_index', [], '/admin/artiste'],
        ];
    }

    private function getUser(string $email): object
    {
        /** @var UserRepository $userRepo */
        $userRepo = static::getContainer()->get(UserRepository::class);

        return $userRepo->findOneBy(['email' => $email]);
    }
}