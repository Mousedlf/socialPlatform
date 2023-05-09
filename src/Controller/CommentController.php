<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comments')]
    public function index(): Response
    {
        $comments=$commentRepository->findAll();

        return $this->render('comment/index.html.twig', [
            'comments'=>$comments
        ]);
    }

    #[Route('/delete/{id}', name:'delete_comment')]
    public function delete(EntityManagerInterface $manager, Comment $comment):Response
    {
        if($comment){
            $manager->remove($comment);
            $manager->flush();
        }

        return $this->redirectToRoute('show_post',[
            'id'=>$comment->getPost()->getId()
        ]);
    }

    #[Route('/create/{id}', name:'create_comment')]
    public function create(Request $request, EntityManagerInterface $manager, Post $post):Response
    {

        if(!$post){
            return $this->redirectToRoute('app_posts');
        }

        $comment= new Comment();
        $formComment=$this->createForm(CommentType::class, $comment);
        $formComment->handleRequest($request);

        if($formComment->isSubmitted() && $formComment->isValid()){

            $comment->setCreatedAt(new \DateTime());
            $comment->setPost($post);

            $manager->persist($comment);
            $manager->flush();

        }

        return $this->redirectToRoute('show_post', [
            'id'=>$comment->getPost()->getId()
        ]);

    }

    #[Route('/edit/{id}', name:'edit_comment')]
    public function edit(EntityManagerInterface $manager, Comment $comment, Request $request):Response
    {
        $formEditComment=$this->createForm(CommentType::class, $comment);
        $formEditComment->handleRequest($request);
        if($formEditComment->isSubmitted() && $formEditComment->isValid()){

            $manager->persist($comment);
            $manager->flush();

            return $this->redirectToRoute('show_post', [
                'id'=>$comment->getPost()->getId()
            ]);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'formEditComment'=>$formEditComment
        ]);
    }


}
