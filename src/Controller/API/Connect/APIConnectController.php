<?php

namespace App\Controller\API\Connect;

use App\Entity\Card;
use App\Entity\UserCard;
use App\Entity\UserNotification;
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

class APIConnectController extends AbstractController
{
    /**
     * @Route("/api/connect/generateCode", name="api.connect.generateCode")
     * @Route("/api/connect/generateCode/")
     */
    public function generateCode()
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $user = $em->getRepository(User::class)->findOneBy(array('id' => $this->getUser()->getID()));

        if($user) {
            $newCode = substr(md5(time() * $user->getID()), 0, 6);
            $user->setConnectCode($newCode);

            $em->persist($user);
            $em->flush();

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $newCode]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        }
    }
    /**
     * @Route("/api/connect/getToken", name="api.connect.getToken")
     * @Route("/api/connect/getToken/")
     */
    public function getToken(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectCode = $request->query->get('connectCode');
        $connectAppApiKey = $request->query->get('connectAppApiKey');

        if($connectCode == "" || $connectAppApiKey == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 400, 'data' => ["connectCode" => $connectCode, "connectAppApiKey" => $connectAppApiKey]]);
            return $response;
        }

        $user = $em->getRepository(User::class)->findOneBy(array('connectCode' => $connectCode));
        $connectApp = $em->getRepository(ConnectApp::class)->findOneBy(array('apiKey' => $connectAppApiKey));

        if($user && $connectApp) {
            $newConnectToken = md5(time() * $user->getID());

            // CLIENT NEXT INCENTIVE, TODO-NEXT: Change ID
            if($connectApp->getID() == 1) {
                $card = $em->getRepository(Card::class)->findOneBy(array('id' => 1));
                $existingUserCard = $em->getRepository(UserCard::class)->findOneBy(array('card' => $card,'user' => $user));
                if($existingUserCard == null) {
                    $newUserCard = new UserCard();
                    $newUserCard->setCard($card);
                    $newUserCard->setUser($user);
                    $newUserCard->setGivenDate(new \DateTime());

                    $em->persist($newUserCard);

                    $newNotification = new UserNotification();
                    $newNotification->setUser($user);
                    $newNotification->setNotificationType(3);
                    $newNotification->setNotificationData("");
                    $newNotification->setConnectedCard($newUserCard->getCard());
                    $newNotification->setConnectedUser($user);

                    $em->persist($newNotification);
                    $em->flush();
                }
            }

            // Create Connection
            $newConnection = new Connection();
            $newConnection->setUser($user);
            $newConnection->setApp($connectApp);
            $newConnection->setConnectDate(new \DateTime('now'));
            $newConnection->setConnectToken($newConnectToken);

            // Output Connection Token

            // Invalidate ConnectionCode
            $user->setConnectCode("");
            $em->persist($user);
            $em->persist($newConnection);
            $em->flush();

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $newConnectToken]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        }
    }

    /**
     * @Route("/api/connect/validateToken", name="api.connect.validateToken")
     * @Route("/api/connect/validateToken/")
     */
    public function validateToken(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connectToken != "" && $connection) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        }
    }
}
