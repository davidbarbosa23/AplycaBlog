<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegisterType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/user", name="user")
     */
    public function index()
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'DashBoard',
        ]);
    }

    /**
     * @Route("/user/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new Users();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        /**
         * If Submit is activated
         */
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * Check if user already exist
             */            
            $em = $this->getDoctrine()->getManager();
            $userCheck = $em->getRepository(Users::class)->findOneBy(['email' => $form['email']->getData()]);
            if ($userCheck) {
                $this->addFlash('success', 'User already registered');
                return $this->redirectToRoute('register');
            }
            
            /**
             * Persistent User
             */
            $user->setPassword($passwordEncoder->encodePassword($user, $form['password']->getData()));
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Registered successfully');
            return $this->redirectToRoute('register');
        }

        return $this->render('user/register.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}
