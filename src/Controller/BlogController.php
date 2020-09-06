<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository(Post::class)->findAll();

        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/blog/myposts", name="myposts")
     */
    public function myposts()
    {
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController Myposts',
        ]);
    }
    
    /**
     * @Route("/blog/single", name="single")
     */
    public function single()
    {
        return $this->render('blog/single.html.twig', [
            'controller_name' => 'BlogController Single',
        ]);
    }
    
    /**
     * @Route("/blog/add", name="add")
     */
    public function add(Request $request)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $post->setAuthor($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();
            return $this->redirectToRoute('blog');
        }

        return $this->render('blog/add.html.twig', [
            'addPostForm' => $form->createView(),
        ]);
    }
}
