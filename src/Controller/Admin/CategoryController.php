<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\AdvertRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category", name="admin_category")
     * @param CategoryRepository $repo
     * @return Response
     */
    public function index(CategoryRepository $repo): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    /**
     * @Route("/admin/add/category", name="admin_category_add")
     * @param Request $request
     * @param CategoryRepository $repo
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function add(Request $request,CategoryRepository $repo, EntityManagerInterface $manager): Response{
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class,$category);
        $categoryForm->handleRequest($request);

        if($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
            $existCategory  = $repo->findOneBy(['name'=>$category->getName()]);
            if($existCategory === null){
                $manager->persist($category);
                $manager->flush();
                $this->addFlash('success',sprintf('L\'utilisateur "%s" a bien été ajouté',$category->getName()));
                return $this->redirectToRoute('admin_category');
            }else {
                $this->addFlash('error', sprintf('La catégorie "%s" ne peux être ajouté car elle existe déjà', $category->getName()));
            }
        }

        return $this->render('admin/category/edit.html.twig', [
            'formCategory' => $categoryForm->createView(),
            'category' => $category
        ]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_category_delete")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Category $category
     * @param AdvertRepository $repository
     * @return Response
     */
    public function delete(EntityManagerInterface $manager, Request $request, Category $category, AdvertRepository $repository):Response
    {
        if($repository->countAdvertInCategory($category)[0]['nbAdvert'] == 0){
            if ($this->isCsrfTokenValid('delete-category-'.$category->getId(), $request->query->get('_token'))) {
                $manager->remove($category);
                $manager->flush();

                $this->addFlash('success','La catégorie a bien été supprimé !');
            }else{
                $this->addFlash('error','Erreur de token csrf, veuillez réessayer.');
            }
        }else{
            $this->addFlash('error','Vous ne pouvez pas supprimer de catégorie contenant des annonces !');
        }

        return $this->redirectToRoute('admin_category');
    }

    /**
     * @Route("/admin/category/edit/{id}",name="admin_category_edit")
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(Category $category, Request $request, EntityManagerInterface $manager):Response
    {
        $categoryForm = $this->createForm(CategoryType::class,$category);

        $categoryForm->handleRequest($request);

        if($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
            $entity = $categoryForm->getData();
            $manager->persist($entity);
            $manager->flush();

            $this->addFlash('success', sprintf('La catégorie "%s" a bien été modifié', $category->getName()));

            return $this->redirectToRoute('admin_category');
        }

        return $this->render('admin/category/edit.html.twig', [
            'formCategory' => $categoryForm->createView(),
            'category' => $category
        ]);
    }
}
