<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Service\FileUploader;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $postsQuery = $em->getRepository(Post::class)->findAllPosts();
        
        // Knp Bundle Paginator
        $pagination = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('blog/index.html.twig', [
            'postsPagination' => $pagination,
        ]);
    }

    /**
     * @Route("/blog/myposts", name="myposts")
     */
    public function myposts(PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        $postsQuery = $em->getRepository(Post::class)->findUserPosts($user->getId());
        
        // Knp Bundle Paginator
        $pagination = $paginator->paginate(
            $postsQuery,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('blog/userposts.html.twig', [
            'postsPagination' => $pagination
        ]);
    }
    
    /**
     * @Route("/blog/{id}", name="single")
     */
    public function single($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);

        return $this->render('blog/single.html.twig', [
            'post' => $post,
        ]);
    }
    
    /**
     * @Route("/blog/add", name="add")
     */
    public function add(Request $request, FileUploader $fileUploader)
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $uploadedImageName = $fileUploader->upload($imageFile, "/post/img");
                $post->setImage($uploadedImageName);
            }

            // Check for current logged user 
            $user = $this->getUser();
            $post->setAuthor($user);

            // Entity Manager
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
