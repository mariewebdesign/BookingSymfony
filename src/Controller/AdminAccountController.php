<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\Pagination;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AdminAccountController extends AbstractController
{
    /**
     * @Route("/admin/login", name="admin_account_login")
     * 
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('admin/account/login.html.twig', [
            
            'hasError'=>$error!==null,
            'username'=>$username
        ]);
    }

    /**
     * Déconnexion de la partie admin
     * @Route("/admin/logout",name="admin_account_logout")
     * 
     * @return void
     */
    public function logout(){


    }

       /**
     * Affichage de la liste des annonces
     * @Route("/admin/users/{page<\d+>?1}", name="admin_users_list")
     */
    public function index(Pagination $paginationService,$page)
    {
        $paginationService->setEntityClass(User::class)
                          ->setPage($page)
                          //->setRoute('admin_users_list')
                            ;

        return $this->render('admin/account/index.html.twig', [
            'pagination'=>$paginationService
        ]);
    }

        /**
     * Permet de modifier une annonce dans la partie admin
     * @Route("admin/users/{id}/edit",name="admin_users_edit")
     * 
     * @param User $user
     * @param Request $request
     * @return Response
     */
    public function edit(User $user, Request $request,ObjectManager $manager){

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success',"L'utilisateur a bien été modifié");
        }

        return $this->render('admin/account/edit.html.twig',[
            'user'=>$user,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Suppression d'une annonce
     * @Route("/admin/users/{id}/delete",name="admin_users_delete")
     *
     * @param User $user
     * @return Response
     */
    public function delete(User $user,ObjectManager $manager){


        if(count($user->getBookings()) > 0 || count($user->getAds()) > 0 || count($user->getComments()) > 0){
            $this->addFlash("warning","Vous ne pouvez pas supprimer un utilisateur qui possède des réservations, des annonces ou des commentaires.");

        }else{
            $manager->remove($user);
            $manager->flush();

            $this->addFlash("success","L'utilisateur <strong>{$user->getfullName()}</strong> a bien été supprimé !");
        }

        return $this->redirectToRoute("admin_users_list");


    }


}
