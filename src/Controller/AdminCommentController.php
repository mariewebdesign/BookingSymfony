<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Service\Pagination;
use App\Form\AdminCommentType;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
     /** 
     * Affichage de la liste des commentaires
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_list")
     */
    public function comments(Pagination $paginationService, $page)
    {

        $paginationService->setEntityClass(Comment::class)
                          ->setLimit(5)
                          ->setPage($page)
                          //->setRoute('admin_comments_list')
                            ;


        return $this->render('admin/comment/index.html.twig', [
            'pagination'=>$paginationService
        ]);
    }

     /**
     * Suppression d'un commentaire
     * @Route("/admin/comments/{id}/delete",name="admin_comments_delete")
     *
     * @param Comment $comment
     * @return Response
     */
    public function delete(Comment $comment,ObjectManager $manager){

        $manager->remove($comment);
        $manager->flush();

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
    public function edit(Comment $comment, Request $request,ObjectManager $manager){

        $form = $this->createForm(AdminCommentType::class,$comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash('success',"Le commentaire a bien été modifiée");
            return $this->redirectToRoute('admin_comments_list');
        }

        return $this->render('admin/comment/edit.html.twig',[
            'comment'=>$comment,
            'form'=>$form->createView()
        ]);
    }

}
