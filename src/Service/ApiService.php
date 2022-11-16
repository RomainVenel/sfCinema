<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class ApiService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getPoster(string $title): string
    {
        $response = $this->client->request(
            'GET',
            'https://imdb8.p.rapidapi.com/title/find',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-RapidAPI-Key' => '',
                    'X-RapidAPI-Host' => 'imdb8.p.rapidapi.com'
                ],
                'query' => [
                    'q' => $title,
                ],
            ]

        );

        $content = $response->toArray();

        $poster = $content['results'][0]['image']['url'];
        return $poster;
    }
}