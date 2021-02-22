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
use App\Entity\UserNotification;

class APIConnectNotificationsController extends AbstractController
{
    /**
     * @Route("/api/connect/notifications", name="api.connect.notifications")
     * @Route("/api/connect/notifications/")
     */
    public function getNotifications(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        if($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            foreach($connection->getUser()->getUserNotifications() as $notification) {
                $data[] = $notification->getJSON();
            }

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }
    
    /**
     * @Route("/api/connect/clearNotification", name="api.connect.clearNotification")
     * @Route("/api/connect/clearNotification/")
     */
    public function clearNotifications(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');
        $notificationIDToClear = $request->query->get('notificationID');

        if($connectToken == "" || $notificationIDToClear == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            $notificationToClear = $em->getRepository(UserNotification::class)->findOneBy(array('id' => intval($notificationIDToClear), 'user' => $connection->getUser()));

            if($notificationToClear) {
                $em->remove($notificationToClear);
                $em->flush();

                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
                return $response;
            } else {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
                return $response;
            }
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }
    
    /**
     * @Route("/api/connect/clearAllNotifications", name="api.connect.clearAllNotifications")
     * @Route("/api/connect/clearAllNotifications/")
     */
    public function clearAllNotifications(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        if($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            $notificationsToClear = $em->getRepository(UserNotification::class)->findBy(array('user' => $connection->getUser()));

            foreach($notificationsToClear as $notificationToClear) {
                $em->remove($notificationToClear);
                $em->flush();
            }

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }
}
