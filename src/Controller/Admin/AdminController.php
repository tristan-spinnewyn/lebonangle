<?php

namespace App\Controller\Admin;

use App\Repository\AdvertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     * @param AdvertRepository $repository
     * @return Response
     */
    public function index(AdvertRepository $repository): Response
    {
        $advertSubmitted = $repository->countByStatus('draft');
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'numberAdvertSubmitted' => $advertSubmitted
        ]);
    }
}
