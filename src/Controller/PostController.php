<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/post')]
class PostController extends AbstractController
{
    #[Route('/', name: 'app_posts')]
    public function index(PostRepository $postRepository): Response
    {
        $posts=$postRepository->findAll();

        return $this->render('post/index.html.twig', [
            'posts'=>$posts
        ]);
    }

    #[Route('/{id}', name:'show_post')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post'=>$post
        ]);

    }

    #[Route('/create', name:'create_post')]
    #[Route('/edit/{id}', name:'edit_post')]
    public function create(Request $request, EntityManagerInterface $manager, Post $post=null):Response
    {
        $edit=false;

        if($post){$edit=true;}
        if(!$edit){$post = new Post();}

        $formPost = $this->createForm(PostType::class, $post);
        $formPost->handleRequest($request);
/*        if($formPost->isSubmitted() && $formPost->isValid()){

            $post->setCreatedAt(new \Datetime());

            $manager->persist($post);
            $manager->flush();

            return $this->redirectToRoute('post_show', [
                'id'=>$post->getId(),
            ]);
        }*/

        return $this->renderForm('post/create.html.twig', [
            'formPost'=>$formPost,
//            'edit'=>$edit,
        ]);
    }
}
