<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Affichage de la liste des annonces
     * @Route("/admin/ads", name="admin_ads_list")
     */
    public function index(AdRepository $repo): Response
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads'=>$repo->findAll()
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
    public function edit(Ad $ad, Request $request){

        $form = $this->createForm(AnnonceType::class,$ad);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($ad);
            $em->flush();

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
    public function delete(Ad $ad){

        $em = $this->getDoctrine()->getManager();

        if(count($ad->getBookings()) > 0 ){
            $this->addFlash("warning","Vous ne pouvez pas supprimer une annonce qui possède des réservations.");

        }else{
            $em->remove($ad);
            $em->flush();

            $this->addFlash("success","L'annonce <strong>{$ad->getTitle()}</strong> a bien été supprimée !");
        }

        return $this->redirectToRoute("admin_ads_list");

    }

}