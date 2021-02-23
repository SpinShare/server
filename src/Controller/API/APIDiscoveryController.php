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
     * @Route("/api/songs/new/{offset}/")
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/songs/updated/{offset}", name="api.songs.updated")
     * @Route("/api/songs/updated/{offset}/")
     */
    public function songsUpdated(Request $request, int $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->getUpdated($offset);
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/songs/hotThisWeek/{offset}", name="api.songs.hotThisWeek")
     * @Route("/api/songs/hotThisWeek/{offset}/")
     */
    public function songsHotThisWeek(Request $request, int $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->getHotThisWeek($offset);
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/songs/hotThisMonth/{offset}", name="api.songs.hotThisMonth")
     * @Route("/api/songs/hotThisMonth/{offset}/")
     */
    public function songsHotThisMonth(Request $request, int $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->getHotThisMonth($offset);
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/search/{searchQuery}", requirements={"searchQuery"=".+"})
     * @Route("/api/search/{searchQuery}/", requirements={"searchQuery"=".+"})
     */
    public function searchParameter(Request $request, string $searchQuery)
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
                                                        ->andWhere('o.publicationStatus IN (0, 1)')
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data['songs'][] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/search", name="api.search")
     * @Route("/api/search/")
     */
    public function search(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $jsonBody = json_decode($request->getContent(), true);

        if($jsonBody == NULL) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        }

        $searchQuery = $jsonBody['searchQuery'];

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
        $resultsSongs = $em->getRepository(Song::class)->createQueryBuilder('o');

        $resultsSongs->where('o.title LIKE :query');
        $resultsSongs->orWhere('o.subtitle LIKE :query');
        $resultsSongs->orWhere('o.tags LIKE :query');
        $resultsSongs->orWhere('o.artist LIKE :query');
        $resultsSongs->orWhere('o.charter LIKE :query');

        // Add Filters for difficulty
        $filterEasy = isset($jsonBody['diffEasy']) ? $jsonBody['diffEasy'] : true;
        $filterNormal = isset($jsonBody['diffNormal']) ? $jsonBody['diffNormal'] : true;
        $filterHard = isset($jsonBody['diffHard']) ? $jsonBody['diffHard'] : true;
        $filterExpert = isset($jsonBody['diffExpert']) ? $jsonBody['diffExpert'] : true;
        $filterXD = isset($jsonBody['diffXD']) ? $jsonBody['diffXD'] : true;

        // Add Filters for difficulty ratings
        $filterMinDifficulty = intval(isset($jsonBody['diffRatingFrom']) ? $jsonBody['diffRatingFrom'] : 0);
        $filterMaxDifficulty = intval(isset($jsonBody['diffRatingTo']) ? $jsonBody['diffRatingTo'] : 99);

        if($filterMinDifficulty == null) { $filterMinDifficulty = 0; }
        if($filterMaxDifficulty == null) { $filterMaxDifficulty = 99; }

        $resultsSongs->setParameter('query', "%".$searchQuery."%");
        $resultsSongs->andWhere('o.publicationStatus IN (0, 1)');
        $resultsSongs->orderBy('o.id', 'DESC');

        $resultsSongs = $resultsSongs->getQuery()->getResult();
        
        // Filter Song Results
        $filteredResultsSongs = [];
        foreach($resultsSongs as $resultSong) {
            // Has the required difficulty
            if($filterEasy && $resultSong->getHasEasyDifficulty() ||
                $filterNormal && $resultSong->getHasNormalDifficulty() ||
                $filterHard && $resultSong->getHasHardDifficulty() ||
                $filterExpert && $resultSong->getHasExtremeDifficulty() ||
                $filterXD && $resultSong->getHasXDDifficulty()) {

                // Has the minimum difficulty rating
                if($resultSong->getHasEasyDifficulty() && $resultSong->getEasyDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasNormalDifficulty() && $resultSong->getNormalDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasHardDifficulty() && $resultSong->getHardDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasExtremeDifficulty() && $resultSong->getExpertDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasXDDifficulty() && $resultSong->getXDDifficulty() >= $filterMinDifficulty) {

                    // Has the maximum difficulty rating
                    if($resultSong->getHasEasyDifficulty() && $resultSong->getEasyDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasNormalDifficulty() && $resultSong->getNormalDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasHardDifficulty() && $resultSong->getHardDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasExtremeDifficulty() && $resultSong->getExpertDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasXDDifficulty() && $resultSong->getXDDifficulty() <= $filterMaxDifficulty) {

                        $filteredResultsSongs[] = $resultSong;
                    }
                }
            }
        }
                
        foreach($filteredResultsSongs as $result) {
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data['songs'][] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/searchAll", name="api.search.all")
     * @Route("/api/searchAll/")
     */
    public function searchAll(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        $data = [];
        $data['users'] = [];
        $data['songs'] = [];

        // Songs
        $resultsSongs = $em->getRepository(Song::class)->findBy(array('publicationStatus' => array(0, 1)), array('id' => 'DESC'));
                
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
            $oneResult['easyDifficulty'] = $result->getEasyDifficulty();
            $oneResult['normalDifficulty'] = $result->getNormalDifficulty();
            $oneResult['hardDifficulty'] = $result->getHardDifficulty();
            $oneResult['expertDifficulty'] = $result->getExpertDifficulty();
            $oneResult['XDDifficulty'] = $result->getXDDifficulty();
            $oneResult['updateDate'] = $result->getUpdateDate();
            $oneResult['updateHash'] = $result->getUpdateHash();
            $oneResult['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data['songs'][] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }

    /**
     * @Route("/api/searchCharts", name="api.searchCharts")
     * @Route("/api/searchCharts/")
     */
    public function searchCharts(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $jsonBody = json_decode($request->getContent(), true);

        if($jsonBody == NULL) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        }

        $searchQuery = $jsonBody['searchQuery'];

        $data = [];

        // Songs
        $resultsSongs = $em->getRepository(Song::class)->createQueryBuilder('o');

        if($searchQuery != "") {
            $resultsSongs->where('o.title LIKE :query');
            $resultsSongs->orWhere('o.subtitle LIKE :query');
            $resultsSongs->orWhere('o.tags LIKE :query');
            $resultsSongs->orWhere('o.artist LIKE :query');
            $resultsSongs->orWhere('o.charter LIKE :query');
        }

        // Add Filters for difficulty
        $filterEasy = isset($jsonBody['diffEasy']) ? $jsonBody['diffEasy'] : true;
        $filterNormal = isset($jsonBody['diffNormal']) ? $jsonBody['diffNormal'] : true;
        $filterHard = isset($jsonBody['diffHard']) ? $jsonBody['diffHard'] : true;
        $filterExpert = isset($jsonBody['diffExpert']) ? $jsonBody['diffExpert'] : true;
        $filterXD = isset($jsonBody['diffXD']) ? $jsonBody['diffXD'] : true;

        // Add Filters for Explicit Content
        $filterExplicit = isset($jsonBody['showExplicit']) ? $jsonBody['showExplicit'] : false;

        // Add Filters for difficulty ratings
        $filterMinDifficulty = intval(isset($jsonBody['diffRatingFrom']) ? $jsonBody['diffRatingFrom'] : 0);
        $filterMaxDifficulty = intval(isset($jsonBody['diffRatingTo']) ? $jsonBody['diffRatingTo'] : 99);

        if($filterMinDifficulty == null) { $filterMinDifficulty = 0; }
        if($filterMaxDifficulty == null) { $filterMaxDifficulty = 99; }

        if($searchQuery != "") {
            $resultsSongs->setParameter('query', "%".$searchQuery."%");
        }
        $resultsSongs->andWhere('o.publicationStatus IN (0, 1)');
        $resultsSongs->orderBy('o.id', 'DESC');

        $resultsSongs = $resultsSongs->getQuery()->getResult();
        
        // Filter Song Results
        $filteredResultsSongs = [];
        foreach($resultsSongs as $resultSong) {
            // Has the required difficulty
            if($filterEasy && $resultSong->getHasEasyDifficulty() ||
                $filterNormal && $resultSong->getHasNormalDifficulty() ||
                $filterHard && $resultSong->getHasHardDifficulty() ||
                $filterExpert && $resultSong->getHasExtremeDifficulty() ||
                $filterXD && $resultSong->getHasXDDifficulty()) {

                // Has the minimum difficulty rating
                if($resultSong->getHasEasyDifficulty() && $resultSong->getEasyDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasNormalDifficulty() && $resultSong->getNormalDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasHardDifficulty() && $resultSong->getHardDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasExtremeDifficulty() && $resultSong->getExpertDifficulty() >= $filterMinDifficulty ||
                    $resultSong->getHasXDDifficulty() && $resultSong->getXDDifficulty() >= $filterMinDifficulty) {

                    // Has the maximum difficulty rating
                    if($resultSong->getHasEasyDifficulty() && $resultSong->getEasyDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasNormalDifficulty() && $resultSong->getNormalDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasHardDifficulty() && $resultSong->getHardDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasExtremeDifficulty() && $resultSong->getExpertDifficulty() <= $filterMaxDifficulty ||
                        $resultSong->getHasXDDifficulty() && $resultSong->getXDDifficulty() <= $filterMaxDifficulty) {

                        if($filterExplicit || $resultSong->getIsExplicit() == $filterExplicit) {
                            $filteredResultsSongs[] = $resultSong;
                        }
                    }
                }
            }
        }
                
        foreach($filteredResultsSongs as $result) {
            $oneResult = [];

            $oneResult = $result->getJSON();
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }
}
