<?php

namespace App\Controller;

use App\Repository\AlbumRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(AlbumRepository $albumRepository): Response
    {
        $eps = $albumRepository->findWithRelations('ep', 12);

        $singles = $albumRepository->findWithRelations('single', 12);

        $albums = $albumRepository->findWithRelations('album', 12);

        return $this->render('home/index.html.twig', [
            'eps' => $eps,
            'singles' => $singles,
            'albums' => $albums,
        ]);
    }
}
