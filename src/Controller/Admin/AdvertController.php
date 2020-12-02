<?php

namespace App\Controller\Admin;

use App\Entity\Advert;
use App\Repository\AdvertRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\WorkflowInterface;

class AdvertController extends AbstractController
{
    /**
     * @Route("/admin/advert", name="admin_advert")
     * @param Request $request
     * @param AdvertRepository $repo
     * @return Response
     */
    public function index(Request $request,AdvertRepository $repo): Response
    {
        $page = $request->query->getInt('p', 1);
        $advertCount = $repo->countPaginateAdmin();

        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $repo->getPaginateAdmin($page),
            'title' => 'Gestion des annonces',
            'page' => $page,
            'nbAdvert' =>$advertCount,
            'type'=> 'all'
        ]);
    }

    /**
     * @Route("/admin/advert_published", name="admin_advert_published")
     * @param Request $request
     * @param AdvertRepository $repo
     * @return Response
     */
    public function advert_published(Request $request,AdvertRepository $repo):Response
    {
        $page = $request->query->getInt('p', 1);
        $nbAdvert = $repo->countByStatus('published');


        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $repo->getPaginateAdminByState($page,'published','advert.publishedAt','DESC'),
            'title' => 'Gestion des annonces publié',
            'page' => $page,
            'nbAdvert' =>$nbAdvert,
            'type'=>'published'
        ]);
    }

    /**
     * @Route("/admin/advert_draft", name="admin_advert_draft")
     * @param Request $request
     * @param AdvertRepository $repo
     * @return Response
     */
    public function advert_draft(Request $request, AdvertRepository $repo):Response
    {
        $page = $request->query->getInt('p', 1);
        $nbAdvert = $repo->countByStatus('draft');


        return $this->render('admin/advert/index.html.twig', [
            'adverts' => $repo->getPaginateAdminByState($page,'draft','advert.createdAt','ASC'),
            'title'=> 'Gestion des annonces soumises',
            'page' => $page,
            'nbAdvert' =>$nbAdvert,
            'type'=>'draft'
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

    /**
     * @Route("/admin/advert/{id}", name="admin_show_advert")
     */

    public function showAdvert(Advert $advert){
        return $this->render('admin/advert/show.html.twig', [
            'advert' => $advert,
        ]);
    }
}
