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
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        if($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            $data['id'] = $connection->getUser()->getId();
            $data['username'] = $connection->getUser()->getUsername();
            $data['isVerified'] = $connection->getUser()->getIsVerified();
            $data['isPatreon'] = $connection->getUser()->getIsPatreon();
            if($connection->getUser()->getCoverReference()) {
                $data['avatar'] = $baseUrl."/uploads/avatar/".$connection->getUser()->getCoverReference();
            } else {
                $data['avatar'] = $baseUrl."/assets/img/defaultAvatar.jpg";
            }

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
