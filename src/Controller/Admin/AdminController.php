<?php

namespace App\Controller\Admin;

use App\Repository\AlbumRepository;
use App\Repository\ArtistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(AlbumRepository $albumRepository, ArtistRepository $artistRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'albums' => $albumRepository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC'], 5),
            'artists' => $artistRepository->findBy([], ['createdAt' => 'DESC', 'id' => 'DESC'], 5),
        ]);
    }
}
