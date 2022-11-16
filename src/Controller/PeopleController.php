<?php

namespace App\Controller;

use App\Entity\People;
use App\Form\PeopleType;
use App\Repository\MovieRepository;
use App\Repository\PeopleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/people', name: 'people_')]
class PeopleController extends AbstractController
{
    #[Route('/list', name: 'index', methods: ['GET'])]
    public function index(PeopleRepository $peopleRepository): Response
    {
        return $this->render('people/index.html.twig', [
            'people' => $peopleRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, PeopleRepository $peopleRepository, MovieRepository $movieRepository): Response
    {
        $this->checkSession($request);

        $person = new People();
        $form = $this->createForm(PeopleType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrayPeople = $request->request->get('people');

            if (!is_null($arrayPeople)) {
                if (array_key_exists('movie', $arrayPeople)) {
                    foreach ($arrayPeople['movie'] as $movieId) {
                        $movie = $movieRepository->find($movieId);
                        $person->addMovie($movie);
                        $movie->addPerson($person);
                    }
                }
            }
            $peopleRepository->save($person, true);

            return $this->redirectToRoute('movie_list');
        }

        return $this->renderForm('people/new.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, People $person, PeopleRepository $peopleRepository, MovieRepository $movieRepository): Response
    {
        $this->checkSession($request);

        $form = $this->createForm(PeopleType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $arrayPeople = $request->request->get('people');

            if (!is_null($arrayPeople)) {
                if (array_key_exists('movie', $arrayPeople)) {
                    foreach ($arrayPeople['movie'] as $movieId) {
                        $movie = $movieRepository->find($movieId);
                        $person->addMovie($movie);
                        $movie->addPerson($person);
                    }
                }
            }
            $peopleRepository->save($person, true);

            return $this->redirectToRoute('movie_list');
        }

        return $this->renderForm('people/edit.html.twig', [
            'person' => $person,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, People $person, PeopleRepository $peopleRepository): Response
    {
        $this->checkSession($request);

        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $peopleRepository->remove($person, true);
        }

        return $this->redirectToRoute('movie_list');
    }

    private function checkSession(Request $request) {

        if (!$request->getSession()->has('user')) {
            throw new AccessDeniedException('Accès interdit');
        }
    }
}
