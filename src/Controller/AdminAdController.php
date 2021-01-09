<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Affichage de la liste des annonces
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_list")
     * 
     */
    public function index(Pagination $paginationService, $page)
    {
        $paginationService->setEntityClass(Ad::class)
                          ->setPage($page)
                          //->setRoute('admin_ads_list')
                            ;
        

        // find() => trouve un objet par rapport à un id
        // findOneBy() => trouve une donnée via des critères de recherche
        // findBy() => trouve plusieurs données grâce à des critères
        
        return $this->render('admin/ad/index.html.twig', [
            'pagination'=>$paginationService  // contient toutes les methodes

            ]);
        }

    /**
     * Permet de modifier une annonce dans la partie admin
     * @Route("admin/ads/{id}/edit",name="admin_ads_edit")
     * 
     * @param Ad $ad
     * @param Request $request
     * @return Response
     */
    public function edit(Ad $ad, Request $request,ObjectManager $manager){

        $form = $this->createForm(AnnonceType::class,$ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($ad);
            $manager->flush();

            $this->addFlash('success',"L'annonce a bien été modifiée");
        }

        return $this->render('admin/ad/edit.html.twig',[
            'ad'=>$ad,
            'form'=>$form->createView()
        ]);
    }

    /**
     * Suppression d'une annonce
     * @Route("/admin/ads/{id}/delete",name="admin_ads_delete")
     *
     * @param Ad $ad
     * @return Response
     */
    public function delete(Ad $ad,ObjectManager $manager){

        if(count($ad->getBookings()) > 0 ){
            $this->addFlash("warning","Vous ne pouvez pas supprimer une annonce qui possède des réservations.");

        }else{
            $manager->remove($ad);
            $manager->flush();

            $this->addFlash("success","L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !");
        }

        return $this->redirectToRoute("admin_ads_list");

    }

}