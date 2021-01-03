<?php

namespace App\Controller;


use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
use Faker\Provider\DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BookingController extends AbstractController
{
    /**
     * Permet d'afficher le formulaire de réservation
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function book(Ad $ad,Request $request)

    {
        $booking = new Booking();

        $form = $this->createForm(BookingType::class,$booking);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()){

            $user = $this->getUser();
            $booking->setBooker($user)
                    ->setAd($ad)
                    ;
          
            // si les dates ne sont pas disponibles

            if(!$booking->isBookableDays()){
                $this->addFlash("warning","Ces dates ne sont pas disponibles, choississez d'autres dates pour votre séjour");
            }
            else{


            $em->persist($booking);
            $em->flush();

            return $this->redirectToRoute("booking_show",['id'=>$booking->getId(),'alert'=>true]);
            }
        }

        return $this->render('booking/book.html.twig', [
           
            'form'=>$form->createView(),
            'ad'=>$ad
        ]);
    }

    /**
     * Affiche une réservation
     * @Route("/booking/{id}",name="booking_show")
     * 
     * @param Booking $booking
     * @return Response
     */
    public function show(Booking $booking){

        return $this->render("booking/show.html.twig",['booking'=>$booking]);
    }
}
