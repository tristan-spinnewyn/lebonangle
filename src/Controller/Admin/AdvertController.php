<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class AdvertController extends AbstractController
{
    /**
     * @Route("/admin/advert", name="admin_advert")
     * @param AdvertRepository $repo
     * @return Response
     */
    public function index(AdvertRepository $repo): Response
    {
        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $repo->findAll(),
            'title' => 'Gestion des annonces'
        ]);
    }

    /**
     * @Route("/admin/advert_published", name="admin_advert_published")
     * @param AdvertRepository $repo
     * @return Response
     */
    public function advert_published(AdvertRepository $repo):Response
    {
        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $repo->findBy(['state'=>'published'],['publishedAt'=>'ASC']),
            'title' => 'Gestion des annonces publié'
        ]);
    }

    /**
     * @Route("/admin/advert_draft", name="admin_advert_draft")
     * @param AdvertRepository $repo
     * @return Response
     */
    public function advert_draft(AdvertRepository $repo):Response
    {
        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $repo->findBy(['state'=>'draft'],['createdAt'=>'DESC']),
            'title'=> 'Gestion des annonces soumises'
        ]);
    }

    /**
     * @Route("/admin/advert/{id}/change-state/{transition}", name="admin_comment_change_state")
     * @param Advert $advert
     * @param string $transition
     * @param WorkflowInterface $advertStateMachine
     * @param EntityManagerInterface $manager
     * @return Response
     */

    public function changeState(Advert $advert, string $transition, WorkflowInterface $advertStateMachine, EntityManagerInterface $manager): Response
    {
        if ($advertStateMachine->can($advert, $transition)) {
            $advertStateMachine->apply($advert, $transition);
            $manager->flush();
            $this->addFlash('success', 'Success');
        } else {
            $this->addFlash('error','An error occured');
        }

        return $this->redirectToRoute('admin_advert');
    }
}
