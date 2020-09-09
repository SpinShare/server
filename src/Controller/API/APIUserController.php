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
use App\Entity\UserCard;
use App\Entity\Promo;

class APIUserController extends AbstractController
{
    /**
     * @Route("/api/user/{userId}", name="api.users.detail")
     */
    public function userDetail(Request $request, int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $data['id'] = $result->getId();
            $data['username'] = $result->getUsername();
            $data['isVerified'] = $result->getIsVerified();
            $data['isPatreon'] = $result->getIsPatreon();
            if($result->getCoverReference()) {
                $data['avatar'] = $baseUrl."/uploads/avatar/".$result->getCoverReference();
            } else {
                $data['avatar'] = $baseUrl."/assets/img/defaultAvatar.jpg";
            }

            // Get User Lists
            $resultsSongs = $em->getRepository(Song::class)->findBy(array('uploader' => $result->getId()), array('uploadDate' => 'DESC'));
            $resultsReviews = $em->getRepository(SongReview::class)->findBy(array('user' => $result->getId()), array('reviewDate' => 'DESC'));
            $resultsSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('user' => $result->getId(), 'isActive' => true), array('submitDate' => 'DESC'));
            $resultsCards = $em->getRepository(UserCard::class)->findBy(array('user' => $result->getId()), array('givenDate' => 'DESC'));

            $data['songs'] = count($resultsSongs);
            $data['reviews'] = count($resultsReviews);
            $data['spinplays'] = count($resultsSpinPlays);
            $data['cards'] = [];
                 
            foreach($resultsCards as $result) {
                $oneResult = [];

                $oneResult['id'] = $result->getId();
                $oneResult['icon'] = $baseUrl."/uploads/card/".$result->getCard()->getIcon();
                $oneResult['title'] = $result->getCard()->getTitle();
                $oneResult['givenDate'] = $result->getGivenDate();
                $oneResult['description'] = $result->getCard()->getDescription();

                $data['cards'][] = $oneResult;
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/user/{userId}/charts", name="api.users.detail.charts")
     */
    public function userDetailCharts(Request $request, int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            // Get User Lists
            $resultsSongs = $em->getRepository(Song::class)->findBy(array('uploader' => $result->getId()), array('uploadDate' => 'DESC'));
                 
            foreach($resultsSongs as $result) {
                $oneResult = [];

                $oneResult['id'] = $result->getId();
                $oneResult['title'] = $result->getTitle();
                $oneResult['subtitle'] = $result->getSubtitle();
                $oneResult['artist'] = $result->getArtist();
                $oneResult['charter'] = $result->getCharter();
                $oneResult['uploader'] = $result->getUploader();
                $oneResult['hasEasyDifficulty'] = $result->getHasEasyDifficulty();
                $oneResult['hasNormalDifficulty'] = $result->getHasNormalDifficulty();
                $oneResult['hasHardDifficulty'] = $result->getHasHardDifficulty();
                $oneResult['hasExtremeDifficulty'] = $result->getHasExtremeDifficulty();
                $oneResult['hasXDDifficulty'] = $result->getHasXDDifficulty();
                $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
                $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

                $data[] = $oneResult;
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/user/{userId}/reviews", name="api.users.detail.reviews")
     */
    public function userDetailReviews(Request $request, int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $resultsReviews = $em->getRepository(SongReview::class)->findBy(array('user' => $result->getId()), array('reviewDate' => 'DESC'));
                 
            foreach($resultsReviews as $result) {
                $data[] = $result->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
    
    /**
     * @Route("/api/user/{userId}/spinplays", name="api.users.detail.spinplays")
     */
    public function userDetailSpinPlays(Request $request, int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $resultsSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('user' => $result->getId(), 'isActive' => true), array('submitDate' => 'DESC'));
                 
            foreach($resultsSpinPlays as $result) {
                $data[] = $result->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
