<?php

namespace App\Controller\API;

use App\Entity\Card;
use App\Entity\UserCard;
use App\Entity\UserNotification;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class APITempController extends AbstractController
{


    /**
     * @Route("/api/temp/start_transmission", name="api.temp.start_transmission")
     * @Route("/api/temp/start_transmission/")
     */
    public function startTransmission(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if(!$user) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        $token = $request->query->get('token');
        if(base64_encode(date('H')) != $token) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        // Give Card
        $card = $em->getRepository(Card::class)->findOneBy(array('id' => 54));
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

        // Redirect to https://spinsha.re/speen-orbital-os/end.php
        return $this->redirect("https://spinsha.re/speen-orbital-os/end.php");
    }
}