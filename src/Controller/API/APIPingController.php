<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\ClientRelease;
use App\Entity\Song;
use App\Entity\SongReview;
use App\Entity\SongSpinPlay;
use App\Entity\User;
use App\Entity\Promo;

class APIPingController extends AbstractController
{
    /**
     * @Route("/api/ping", name="api.ping")
     */
    public function ping()
    {
        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'pong' => true]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
