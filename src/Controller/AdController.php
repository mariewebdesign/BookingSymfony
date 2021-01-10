<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Service\Pagination;
use App\Repository\AdRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{
    /**
     * Permet d'afficher une liste d'annonces
     * @Route("/ads/{page<\d+>?1}", name="ads_list")
     * @return Response
     */
    public function index(Pagination $paginationService,$page){

        // via $repo, on va aller chercher toutes les annonces via la méthode findAll

        $paginationService->setEntityClass(Ad::class)
                          ->setPage($page)
       
          ;

        
        return $this->render('ad/index.html.twig', [
            'controller_name' => 'Nos annonces',
            'pagination'=>$paginationService
        ]);
    }
   
    /**
  * Permet de créer une annonce
  * @Route("ads/new",name="ads_create")
  * @IsGranted("ROLE_USER")
  * @return response
  */
    public function create(Request $request,ObjectManager $manager){

        // fabricant de formulaire : FORMBUILDER

        $ad = new Ad();

        // on lance la fabrication et la configuration de notre formulaire
        $form = $this->createForm(AnnonceType::class,$ad);

        // récupération des données du formulaire
        $form -> handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // Si le formulaire est soumis ET si le formulaire est valide, on demande à Doctrine 
            // de sauvegarder ces données dans l'objet manager

            // pour chaque image supplémnentaire ajoutée
            foreach($ad->getImages() as $image){

                // on relie l'image à l'annonce et on modifie l'annonce
                $image->setAd($ad);

                // on sauvegarde les images

                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());
            $manager->persist($ad);
            
            $manager->flush();

            $this->addFlash('success',"Annonce <strong>{$ad->getTitle()}</strong> créée avec succès");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);

        }

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

    /**
    * Permet d'éditer et de modifier un article
    * @Route("/ads/{slug}/edit",name="ads_edit")
    * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Cet annonce ne vous appartient pas, vous ne pouvez pas la modifier")
    * @return Response
    */

    public function edit(Ad $ad,Request $request,ObjectManager $manager){

        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            foreach($ad->getImages() as $image){

                // on relie l'image à l'annonce et on modifie l'annonce
                $image->setAd($ad);

                // on sauvegarde les images
                $manager->persist($image);
            }

     
            $manager->persist($ad);           
            $manager->flush();

            $this->addFlash("success","les modifications ont été faites !");

            return $this->redirectToRoute('ads_single',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/edit.html.twig',['form'=>$form->createView(),'ad'=>$ad]);


    }

    /**
     * Suppression de l'annonce
     * @Route("/ads/{slug}/delete",name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Vous n'avez pas le droit d'accéder à cette page !")
     * @param Ad $ad
     * @return Response
     */
    public function delete(Ad $ad,ObjectManager $manager){
        
        $manager->remove($ad);
        $manager->flush();
        $this->addFlash("success","L'annonce <em>{$ad->getTitle()}</em> a bien été supprimée");

        return $this->redirectToRoute("account_home");
    }

}
