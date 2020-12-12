<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Permet d'afficher une liste d'annonces
     * @Route("/ads", name="ads_list")
     * @return Response
     */
    public function index(AdRepository $repo){

        // via $repo, on va aller chercher toutes les annonces via la méthode findAll

        $ads = $repo->findAll();
        
        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'ads'=>$ads
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("ads/new",name="ads_create")
     * @return Response
     */
    public function create(){

        // fabricant de formulaire : FORMBUILDER

        $ad = new Ad();

        // on lance la fabrication et la configuration de notre formulaire
        $form = $this->createForm(AnnonceType::class,$ad);
                 
                    
        return $this->render('ad/new.html.twig',['form'=>$form->createView()]);
    }
    
    /**
     * Permet d'affiche une seule annonce
     * @Route("/ads/{slug}",name="ads_single")
     * @return Response
     */
    public function show($slug,Ad $ad){

        // je récupère l'annonce qui correspond au slug
        // findOneByX dont X = 1 champ de la table, à préciser à la place de X

        // findByX = renvoie un tableau d'annonces (plusieurs éléments)
       
       // $ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig',['ad'=>$ad]);

    }

}
