<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIPingController extends AbstractController
{
    /**
     * @Route("/api/ping", name="api.ping")
     * @Route("/api/ping/")
     */
    public function ping()
    {
        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'pong' => true]);
        return $response;
    }
}
