<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Repository\MovieRepository;
use App\Repository\PeopleRepository;
use App\Service\ApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', name: 'movie_')]
class MovieController extends AbstractController
{
    #[Route('/', name: 'list', methods: ['GET'])]
    public function index(Request $request, MovieRepository $movieRepository): Response
    {
        $page = $request->query->getInt('page', 1);
        $movies = $this->forward('App\Controller\ApiController::movies', [
            'page' => $page,
        ]);
        $movies = json_decode($movies->getContent(), true);
        $finalMovies['pages'] = $movies['pages'];
        $finalMovies['page'] = $movies['page'];
        $finalMovies['limit'] = $movies['limit'];
        foreach ($movies['data'] as $movie) {
            $movieById = $movieRepository->find($movie['id']);
            $movie['type'] = $movieById->getType();
            $movie['people'] = $movieById->getPeople();
            $finalMovies['data'][] = $movie;
        }

        $connectedData = [];
        if ($request->getSession()->has('user')) {
            $connectedData['firstname'] = $request->getSession()->get('user')->getFirstname();
            $connectedData['lastname'] = $request->getSession()->get('user')->getLastname();
        }

        return $this->render('movie/index.html.twig', [
            'movies' => $finalMovies,
            'connected' => $request->getSession()->has('user'),
            'connectedData' => $connectedData
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, PeopleRepository $peopleRepository): Response
    {
        $this->checkSession($request);

        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrayMovie = $request->request->get('movie');

            if (!is_null($arrayMovie)) {
                if (array_key_exists('movie', $arrayMovie)) {
                    foreach ($arrayMovie['people'] as $peopleId) {
                        $people = $peopleRepository->find($peopleId);
                        $movie->addPerson($people);
                        $people->addMovie($movie);
                    }
                }
            }
            $entityManager->persist($movie);
            $entityManager->flush();

            return $this->redirectToRoute('movie_list');
        }

        return $this->renderForm('movie/new.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Movie $movie, EntityManagerInterface $entityManager, PeopleRepository $peopleRepository): Response
    {

        $this->checkSession($request);

        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $arrayMovie = $request->request->get('movie');

            if (!is_null($arrayMovie)) {
                if (array_key_exists('movie', $arrayMovie)) {
                    foreach ($arrayMovie['people'] as $peopleId) {
                        $people = $peopleRepository->find($peopleId);
                        $movie->addPerson($people);
                        $people->addMovie($movie);
                    }
                }
            }
            $entityManager->persist($movie);
            $entityManager->flush();

            return $this->redirectToRoute('movie_list');
        }

        return $this->renderForm('movie/edit.html.twig', [
            'movie' => $movie,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Movie $movie, EntityManagerInterface $entityManager): Response
    {
        $this->checkSession($request);

        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($movie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('movie_list');
    }

    private function checkSession(Request $request) {

        if (!$request->getSession()->has('user')) {
            throw new AccessDeniedException('Accès interdit');
        }
    }
}
