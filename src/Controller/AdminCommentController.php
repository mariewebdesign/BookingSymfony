<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
     /** 
     * Affichage de la liste des commentaires
     * @Route("/admin/comments", name="admin_comments_list")
     */
    public function comments(CommentRepository $repo): Response
    {
        return $this->render('admin/comment/index.html.twig', [
            'comments'=>$repo->findAll()
        ]);
    }

     /**
     * Suppression d'un commentaire
     * @Route("/admin/comments/{id}/delete",name="admin_comments_delete")
     *
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment){

        $em = $this->getDoctrine()->getManager();

        $em->remove($comment);
        $em->flush();

        $this->addFlash("success","Le commentaire a bien été supprimé !");


        return $this->redirectToRoute("admin_comments_list");

    }

    /**
     * Edition des commentaires
     * @Route("admin/comments/{id}/edit",name="admin_comments_edit")
     * @param Comment $comment
     * @param Request $request
     * @return Response
     */
    public function edit(Comment $comment, Request $request){

        $form = $this->createForm(AdminCommentType::class,$comment);

        $form->handleRequest($request);

        $em = $this->getDoctrine()->getManager();

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success',"Le commentaire a bien été modifiée");
            return $this->redirectToRoute('admin_comments_list');
        }

        return $this->render('admin/comment/edit.html.twig',[
            'comment'=>$comment,
            'form'=>$form->createView()
        ]);
    }

}
