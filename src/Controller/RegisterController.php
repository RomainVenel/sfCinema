<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $doctrine->getManager();

                /** @var $user User */
                $user = $form->getData();

                // Hash du mdp pour éviter le stockage en clair
                $password = $hasher->hashPassword($user, $user->getPassword());
                $user->setPassword($password);
                $em->persist($user);
                $em->flush();

                return $this->redirectToRoute('movie_list');

            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
