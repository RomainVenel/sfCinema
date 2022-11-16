<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'connexion')]
    public function index(Request $request, ManagerRegistry $doctrine, UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(ConnexionType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $data = $request->request->get('connexion');

                /** @var $user User */
                $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $data['email']]);

                if (!is_null($user)) {
                    if ($hasher->isPasswordValid($user, $data['password'])) {

                        $session = $request->getSession();
                        $session->set('user', $user);

                        return $this->redirectToRoute('movie_list');
                    }
                }

                return $this->render('connexion/index.html.twig', [
                    'failed' => 'Mail et/ou mot de passe incorrect(s)',
                    'form' => $form->createView()
                ]);

            }
        }

        return $this->render('connexion/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/logout', name: 'logout')]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirectToRoute('movie_list');
    }
}
