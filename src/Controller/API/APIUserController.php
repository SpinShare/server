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

            // Get User Songs
            $resultsSongs = $em->getRepository(Song::class)->findBy(array('uploader' => $result->getId()), array('uploadDate' => 'DESC'));

            $data['songs'] = [];
                 
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

                $data['songs'][] = $oneResult;
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
