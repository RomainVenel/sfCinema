<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\PeopleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api', name: 'api_')]
class ApiController extends AbstractController
{
    #[Route('/movies/{page}', name: 'movies', methods: ['GET'])]
    public function movies(MovieRepository $movieRepository, $page = 1): Response
    {
        $movies = $movieRepository->findAllPaginated($page, 4);

        return $this->json($movies, 200, ["Content-Type" => "application/json"]);
    }

    #[Route('/movie/{movieId}', name: 'showMovie', methods: ['GET'])]
    public function showMovie(MovieRepository $movieRepository, $movieId): Response
    {
        $movie = $movieRepository->find($movieId);

        return $this->json($movie->toArray(), 200, ["Content-Type" => "application/json"]);
    }

    #[Route('/people/{page}', name: 'people', methods: ['GET'])]
    public function people(PeopleRepository $peopleRepository, $page = 1): Response
    {
        $people = $peopleRepository->findAllPaginated($page, 4);

        return $this->json($people, 200, ["Content-Type" => "application/json"]);
    }

    #[Route('/onePeople/{peopleId}', name: 'showPeople', methods: ['GET'])]
    public function showPeople(PeopleRepository $peopleRepository, $peopleId): Response
    {
        $people = $peopleRepository->find($peopleId);

        return $this->json($people->toArray(), 200, ["Content-Type" => "application/json"]);
    }
}
