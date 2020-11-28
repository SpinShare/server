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

class APIDiscoveryController extends AbstractController
{
    /**
     * @Route("/api/songs/new/{offset}", name="api.songs.new")
     */
    public function songsNew(Request $request, int $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->getNew($offset);
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        foreach($results as $result) {
            $oneResult = [];

            $oneResult['id'] = $result->getId();
            $oneResult['title'] = $result->getTitle();
            $oneResult['subtitle'] = $result->getSubtitle();
            $oneResult['artist'] = $result->getArtist();
            $oneResult['charter'] = $result->getCharter();
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

    /**
     * @Route("/api/songs/hot/{offset}", name="api.songs.hot")
     */
    public function songsHot(Request $request, int $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->getHot($offset);
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        foreach($results as $result) {
            $oneResult = [];

            $oneResult['id'] = $result->getId();
            $oneResult['title'] = $result->getTitle();
            $oneResult['subtitle'] = $result->getSubtitle();
            $oneResult['artist'] = $result->getArtist();
            $oneResult['charter'] = $result->getCharter();
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

    /**
     * @Route("/api/songs/popular/{offset}", name="api.songs.popular")
     */
    public function songsPopular(Request $request, int $offset = 0)
    {
        // TODO: Remove this later on
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/api/search/{searchQuery}", name="api.search")
     */
    public function search(Request $request, string $searchQuery)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $data = [];
        $data['users'] = [];
        $data['songs'] = [];

        // Users
        $resultsUsers = $em->getRepository(User::class)->createQueryBuilder('o')
                                                        ->where('o.username LIKE :query')
                                                        ->orderBy('o.id', 'DESC')
                                                        ->setParameter('query', '%'.$searchQuery.'%')
                                                        ->getQuery()
                                                        ->getResult();

        foreach($resultsUsers as $result) {
            $oneResult = [];

            $oneResult['id'] = $result->getId();
            $oneResult['username'] = $result->getUsername();
            $oneResult['isVerified'] = $result->getIsVerified();
            $oneResult['isPatreon'] = $result->getIsPatreon();
            if($result->getCoverReference()) {
                $oneResult['avatar'] = $baseUrl."/uploads/avatar/".$result->getCoverReference();
            } else {
                $oneResult['avatar'] = $baseUrl."/assets/img/defaultAvatar.jpg";
            }

            $data['users'][] = $oneResult;
        }

        // Songs
        $resultsSongs = $em->getRepository(Song::class)->createQueryBuilder('o')
                                                        ->where('o.title LIKE :query')
                                                        ->orWhere('o.subtitle LIKE :query')
                                                        ->orWhere('o.tags LIKE :query')
                                                        ->orWhere('o.artist LIKE :query')
                                                        ->orWhere('o.charter LIKE :query')
                                                        ->orderBy('o.id', 'DESC')
                                                        ->setParameter('query', '%'.$searchQuery.'%')
                                                        ->getQuery()
                                                        ->getResult();
                
        foreach($resultsSongs as $result) {
            $oneResult = [];

            $oneResult['id'] = $result->getId();
            $oneResult['title'] = $result->getTitle();
            $oneResult['subtitle'] = $result->getSubtitle();
            $oneResult['artist'] = $result->getArtist();
            $oneResult['charter'] = $result->getCharter();
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

    /**
     * @Route("/api/searchAll", name="api.search.all")
     */
    public function searchAll(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $data = [];
        $data['users'] = [];
        $data['songs'] = [];

        // Songs
        $resultsSongs = $em->getRepository(Song::class)->findBy(array(), array('id' => 'DESC'));
                
        foreach($resultsSongs as $result) {
            $oneResult = [];

            $oneResult['id'] = $result->getId();
            $oneResult['title'] = $result->getTitle();
            $oneResult['subtitle'] = $result->getSubtitle();
            $oneResult['artist'] = $result->getArtist();
            $oneResult['charter'] = $result->getCharter();
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
