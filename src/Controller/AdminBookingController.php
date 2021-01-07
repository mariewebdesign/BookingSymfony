<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Repository\BookingRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminBookingController extends AbstractController
{
    /**
     * Affichage de la liste des réservations
     * @Route("/admin/bookings", name="admin_bookings_list")
     * 
     * @return Response
     */
    public function index(BookingRepository $repo)
    {
        return $this->render('admin/booking/index.html.twig', [
            'bookings' => $repo->findAll()
        ]);
    }

         /**
     * Suppression d'une réservation
     * @Route("/admin/bookings/{id}/delete",name="admin_bookings_delete")
     *
     * @param Booking $booking
     * @return Response
     */
    public function delete(Booking $booking){

        $em = $this->getDoctrine()->getManager();

        $em->remove($booking);
        $em->flush();

        $this->addFlash("success","La réservation n°{$booking->getId()} a bien été supprimé !");


        return $this->redirectToRoute("admin_bookings_list");

    }

    /**
     * Edition des réservations
     * @Route("admin/bookings/{id}/edit",name="admin_bookings_edit")
     * @param booking $booking
     * @param Request $request
     * @return Response
     */
    public function edit(booking $booking, Request $request){

        $form = $this->createForm(AdminBookingType::class,$booking);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()){

           // $booking->setAmount($booking->getAd()->getPrice() * $booking->getDuration());
            $booking->setAmount(0);

            $em->persist($booking);
            $em->flush();

            $this->addFlash('success',"La réservation n°{$booking->getId()} a bien été modifiée");
            return $this->redirectToRoute('admin_bookings_list');
        }

        return $this->render('admin/booking/edit.html.twig',[
            'booking'=>$booking,
            'form'=>$form->createView()
        ]);
    }
}
