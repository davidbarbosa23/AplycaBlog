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
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new Users();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Entity Manager
            $em = $this->getDoctrine()->getManager();

            // Check if user already exist
            $userCheck = $em->getRepository(Users::class)->findOneBy(['email' => $form['email']->getData()]);
            if ($userCheck) {
                $this->addFlash('error', 'User already registered');
                return $this->redirectToRoute('register');
            }
            
            // Set password with encoder
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
