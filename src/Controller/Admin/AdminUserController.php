<?php

namespace App\Controller\Admin;

use App\Entity\AdminUser;
use App\Form\AdminUserType;
use App\Repository\AdminUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/user", name="admin_user")
     * @param AdminUserRepository $repo
     * @return Response
     */
    public function index(AdminUserRepository $repo): Response
    {
        return $this->render('admin/admin_user/index.html.twig', [
            'admins' => $repo->findAll()
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="admin_user_edit")
     * @param AdminUserRepository $repo
     * @param AdminUser $adminUser
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */

    public function edit(AdminUserRepository $repo, AdminUser $adminUser, EntityManagerInterface $manager, Request $request): Response
    {
        $adminUserForm = $this->createForm(AdminUserType::class, $adminUser);

        $adminUserForm->handleRequest($request);
        if ($adminUserForm->isSubmitted() && $adminUserForm->isValid()) {
            $entity = $adminUserForm->getData();
            $manager->persist($entity);
            $manager->flush();

            $this->addFlash('success', sprintf('L\'utilisateur "%s" a bien été modifié', $adminUser->getUsername()));

            return $this->redirectToRoute('admin_user');
        }

        return $this->render('admin/admin_user/edit.html.twig', [
            'formAdmin' => $adminUserForm->createView(),
            'user' => $adminUser
        ]);
    }

    /**
     * @Route("/admin/add", name="admin_user_add")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function add(EntityManagerInterface $manager, Request $request, AdminUserRepository $repo): Response
    {
        $adminUser = new AdminUser();

        $adminUserForm = $this->createForm(AdminUserType::class, $adminUser);

        $adminUserForm->handleRequest($request);
        if ($adminUserForm->isSubmitted() && $adminUserForm->isValid()) {
            $existUserAdmin = $repo->findOneBy(['username' => $adminUser->getUsername()]);
            if ($existUserAdmin === null) {
                $manager->persist($adminUser);
                $manager->flush();
                $this->addFlash('success', sprintf('L\'utilisateur "%s" a bien été ajouté', $adminUser->getUsername()));
                return $this->redirectToRoute('admin_user');
            } else {
                $this->addFlash('error', sprintf('L\'utilisateur "%s" ne peux être ajouté car il existe déjà', $adminUser->getUsername()));
            }
        }


        return $this->render('admin/admin_user/edit.html.twig', [
            'formAdmin' => $adminUserForm->createView(),
            'user' => $adminUser
        ]);
    }

    /**
     * @Route("/admin/delete/{id}", name="admin_user_delete")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param AdminUser $adminUser
     * @param AdminUserRepository $repository
     * @return Response
     */
    public function delete(EntityManagerInterface $manager, Request $request, AdminUser $adminUser, AdminUserRepository $repository): Response
    {
        if ($adminUser->getUsername() !== $this->getUser()->getUsername()) {
            if ($repository->countAdminUser()[0]['nbUser'] > 1) {
                if ($this->isCsrfTokenValid('delete-user-' . $adminUser->getId(), $request->query->get('_token'))) {
                    $manager->remove($adminUser);
                    $manager->flush();

                    $this->addFlash('success', 'L\'utilisateur a bien été supprimé !');
                } else {
                    $this->addFlash('error', 'Erreur de token csrf, veuillez réessayer.');
                }
            } else {
                $this->addFlash('error', 'Vous devez laissez un compte admin obligatoirement.');
            }
        } else {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre compte');
        }

        return $this->redirectToRoute('admin_user');
    }
}
