<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\GenreRepository;
use App\Repository\AlbumRepository;

final class AjaxController extends AbstractController
{
    #[Route('/ajax/test', name: 'app_ajax')]
    public function index(): Response
    {
        return $this->json(['message' => 'Hello from AjaxController!']);
    }

    #[Route('/ajax/test2', name: 'app_ajax_test2')]
    public function test2(): Response
    {
        $nb = 5;
        return $this->json(['nb' => $nb]);
    }

    #[Route('/ajax/count_genre', name: 'app_ajax_count_genre')]
    public function countGenre(GenreRepository $genreRepository): Response
    {
        $entities = $genreRepository->findAll();
        $count = count($entities);
        return $this->json(['total_genres' => $count]);
    }

    #[Route('/ajax/genre_pokemon', name: 'app_ajax_genre_pokemon')]
    public function genrePokemon(GenreRepository $genreRepository): Response
    {
        $genre = $genreRepository->findOneBy(['name' => 'pokemon']);
        return $this->json(['exists' => $genre !== null]);
    }

    #[Route('/ajax/test-serialization', name: 'app_ajax_test_serialization')]
    public function testSerialization(GenreRepository $genreRepository): Response
    {
        $genres = $genreRepository->findAll();
        return $this->json(['genres' => $genres], 200, [], ['groups' => 'genre:read']);
    }

    #[Route('/ajax/test-serialization-album', name: 'app_ajax_test_serialization_album')]
    public function testSerializationAlbum(AlbumRepository $albumRepository): Response
    {
        $albums = $albumRepository->findAll();
        return $this->json(['albums' => $albums], 200, [], ['groups' => 'album:read']);
    }
}
