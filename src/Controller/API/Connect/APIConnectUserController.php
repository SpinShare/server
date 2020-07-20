<?php

namespace App\Controller\API\Connect;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\User;
use App\Entity\Connection;
use App\Entity\ConnectApp;

class APIConnectUserController extends AbstractController
{

    /**
     * @Route("/api/connect/profile/", name="api.connect.profile")
     */
    public function getProfile(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        // TODO: Find Connection
        $user = $em->getRepository(User::class)->findOneBy(array('connectToken' => $connectToken));

        if($connectToken != "" && $user) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
