<?php

namespace App\Controller\API\Connect;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

use App\Entity\User;
use App\Entity\Connection;
use App\Entity\ConnectApp;
use App\Entity\UserNotification;

class APIConnectNotificationsController extends AbstractController
{
    private const NOTIFICATIONS_TTL = 3600;

    /**
     * @Route("/api/connect/notifications", name="api.connect.notifications")
     * @Route("/api/connect/notifications/")
     */
    public function getNotifications(Request $request, CacheInterface $cache)
    {
        $em = $this->getDoctrine()->getManager();

        $connectToken = $request->query->get('connectToken');

        if ($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            $response->setSharedMaxAge(3000);
            $response->setMaxAge(3000);
            return $response;
        }

        $cacheKey = 'connect_notifications_' . preg_replace('/[^a-zA-Z0-9_]/', '', $connectToken);

        $cached = $cache->get($cacheKey, function (ItemInterface $item) use ($em, $connectToken) {
            $item->expiresAfter(self::NOTIFICATIONS_TTL);

            $connection = $em->getRepository(Connection::class)->findOneBy(['connectToken' => $connectToken]);

            if (!$connection) {
                return ['status' => 403, 'data' => []];
            }

            $data = [];
            foreach ($connection->getUser()->getUserNotifications() as $notification) {
                $data[] = $notification->getJSON();
            }

            return ['status' => 200, 'data' => $data];
        });

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => $cached['status'], 'data' => $cached['data']]);
        $response->setSharedMaxAge(self::NOTIFICATIONS_TTL);
        $response->setMaxAge(self::NOTIFICATIONS_TTL);
        return $response;
    }
    
    /**
     * @Route("/api/connect/clearNotification", name="api.connect.clearNotification")
     * @Route("/api/connect/clearNotification/")
     */
    public function clearNotifications(Request $request, CacheInterface $cache)
    {
        $em = $this->getDoctrine()->getManager();

        $connectToken = $request->query->get('connectToken');
        $notificationIDToClear = $request->query->get('notificationID');

        if ($connectToken == "" || $notificationIDToClear == "") {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(['connectToken' => $connectToken]);

        if ($connection) {
            $notificationToClear = $em->getRepository(UserNotification::class)->findOneBy(['id' => intval($notificationIDToClear), 'user' => $connection->getUser()]);

            if ($notificationToClear) {
                $em->remove($notificationToClear);
                $em->flush();

                $cache->delete('connect_notifications_' . preg_replace('/[^a-zA-Z0-9_]/', '', $connectToken));

                return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
            } else {
                return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            }
        } else {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
        }
    }

    /**
     * @Route("/api/connect/clearAllNotifications", name="api.connect.clearAllNotifications")
     * @Route("/api/connect/clearAllNotifications/")
     */
    public function clearAllNotifications(Request $request, CacheInterface $cache)
    {
        $em = $this->getDoctrine()->getManager();

        $connectToken = $request->query->get('connectToken');

        if ($connectToken == "") {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(['connectToken' => $connectToken]);

        if ($connection) {
            $notificationsToClear = $em->getRepository(UserNotification::class)->findBy(['user' => $connection->getUser()]);

            foreach ($notificationsToClear as $notificationToClear) {
                $em->remove($notificationToClear);
            }
            $em->flush();

            $cache->delete('connect_notifications_' . preg_replace('/[^a-zA-Z0-9_]/', '', $connectToken));

            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
        } else {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
        }
    }
}
