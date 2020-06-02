<?php

namespace App\Controller;

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

class APIController extends AbstractController
{
    public $apiVersion = 1;
    public $clientVersion = [1, 0, 0];

    /**
     * @Route("/api/ping", name="api.ping")
     */
    public function ping()
    {
        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'pong' => true]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/api/streamStatus", name="api.streamStatus")
     */
    public function streamStatus()
    {
        $client = HttpClient::create();
        $apiAccessResponseRaw = $client->request('POST', 'https://id.twitch.tv/oauth2/token?client_id='.$_ENV['TWITCH_API_CLIENT_ID'].'&client_secret='.$_ENV['TWITCH_API_CLIENT_SECRET'].'&grant_type=client_credentials');
        $apiAccessResponse = json_decode($apiAccessResponseRaw->getContent());

        $apiAccessToken = $apiAccessResponse->access_token;

        $apiResponseRaw = $client->request('GET', 'https://api.twitch.tv/helix/streams/?user_login=spinshare', [
            'headers' => [
                'Client-ID' => $_ENV['TWITCH_API_CLIENT_ID'],
                'Authorization' => 'Bearer '.$apiAccessToken
            ],
        ]);
        $apiResponse = json_decode($apiResponseRaw->getContent());

        if(count($apiResponse->data) != 0) {
            $data = [
                "title" => $apiResponse->data[0]->title,
                "viewers" => $apiResponse->data[0]->viewer_count,
                "isLive" => ($apiResponse->data[0]->type == "live") ? true : false
            ];
        } else {
            $data = [
                "title" => "",
                "viewers" => 0,
                "isLive" => false
            ];
        }

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/api/latestVersion/{platform}", name="api.latestVersion")
     */
    public function latestVersion(string $platform)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $latestVersion = $em->getRepository(ClientRelease::class)->findOneBy(array('platform' => $platform), array('majorVersion' => 'DESC', 'minorVersion' => 'DESC', 'patchVersion' => 'DESC'));
        $data['stringVersion'] = $latestVersion->getMajorVersion().".".$latestVersion->getMinorVersion().".".$latestVersion->getPatchVersion();
        $data['majorVersion'] = $latestVersion->getMajorVersion();
        $data['minorVersion'] = $latestVersion->getMinorVersion();
        $data['patchVersion'] = $latestVersion->getPatchVersion();

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

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
            $oneResult['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
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
            $oneResult['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/api/songs/popular/{offset}", name="api.songs.popular")
     */
    public function songsPopular(Request $request, int $offset = 0)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->getPopular($offset);
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
            $oneResult['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }

    /**
     * @Route("/api/song/{idOrReference}", name="api.songs.detail")
     */
    public function songDetail(Request $request, $idOrReference)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if(is_numeric($idOrReference)) {
            // $idOrReference is the ID
            $result = $em->getRepository(Song::class)->findOneBy(array('id' => $idOrReference));
        } else {
            // $idOrReference is the file Reference
            $result = $em->getRepository(Song::class)->findOneBy(array('fileReference' => $idOrReference));
        }
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $result->setViews($result->getViews() + 1);
            $em->persist($result);
            $em->flush();

            $data['id'] = $result->getId();
            $data['title'] = $result->getTitle();
            $data['subtitle'] = $result->getSubtitle();
            $data['artist'] = $result->getArtist();
            $data['charter'] = $result->getCharter();
            $data['hasEasyDifficulty'] = $result->getHasEasyDifficulty();
            $data['hasNormalDifficulty'] = $result->getHasNormalDifficulty();
            $data['hasHardDifficulty'] = $result->getHasHardDifficulty();
            $data['hasExtremeDifficulty'] = $result->getHasExtremeDifficulty();
            $data['hasXDDifficulty'] = $result->getHasXDDifficulty();
            $data['uploader'] = $result->getUploader();
            $data['uploadDate'] = $result->getUploadDate();
            $data['tags'] = $result->getTagsArray();
            $data['paths']['ogg'] = $baseUrl."/uploads/audio/".$result->getFileReference()."_0.ogg";
            $data['paths']['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
            $data['paths']['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
            $data['description'] = $result->getDescription();
            $data['views'] = $result->getViews();
            $data['downloads'] = $result->getDownloads();
    
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/song/{idOrReference}/reviews", name="api.songs.detail.reviews")
     */
    public function songDetailReviews(Request $request, $idOrReference)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if(is_numeric($idOrReference)) {
            // $idOrReference is the ID
            $resultSong = $em->getRepository(Song::class)->findOneBy(array('id' => $idOrReference));
        } else {
            // $idOrReference is the file Reference
            $resultSong = $em->getRepository(Song::class)->findOneBy(array('fileReference' => $idOrReference));
        }
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$resultSong) {
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $resultReviewAverage = $em->getRepository(SongReview::class)->getAveragebyID($resultSong->getId());
            $data['average'] = $resultReviewAverage;
            
            $resultReviews = $em->getRepository(SongReview::class)->findBy(array('song' => $resultSong), array('reviewDate' => 'DESC'));

            foreach($resultReviews as $review) {
                $data['reviews'][] = $review->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/song/{idOrReference}/spinplays", name="api.songs.detail.spinplays")
     */
    public function songDetailSpinPlays(Request $request, $idOrReference)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if(is_numeric($idOrReference)) {
            // $idOrReference is the ID
            $resultSong = $em->getRepository(Song::class)->findOneBy(array('id' => $idOrReference));
        } else {
            // $idOrReference is the file Reference
            $resultSong = $em->getRepository(Song::class)->findOneBy(array('fileReference' => $idOrReference));
        }
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$resultSong) {
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('song' => $resultSong, 'isActive' => true), array('submitDate' => 'DESC'));

            foreach($resultSpinPlays as $spinPlay) {
                $data['spinPlays'][] = $spinPlay->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/song/{id}/download", name="api.songs.download")
     */
    public function songDownload(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $id));

        $zipLocation = $this->getParameter('temp_path').DIRECTORY_SEPARATOR;
        $zipName = $result->getFileReference().".zip";

        if(!$result) {
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            try {
                $coverFiles = glob($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$result->getFileReference().".png");
                $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$result->getFileReference()."_*.ogg");

                $zip = new \ZipArchive;
                $zip->open($zipLocation.$zipName, \ZipArchive::CREATE);
                $zip->addFile($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$result->getFileReference().".srtb", $result->getFileReference().".srtb");
                if(is_file($coverFiles[0])) {
                    $zip->addFile($coverFiles[0], "AlbumArt".DIRECTORY_SEPARATOR.$result->getFileReference().".png");
                }
                if(count($oggFiles) > 0) {
                    foreach($oggFiles as $oggFile) {
                        $oggIndex = explode(".", explode("_", $oggFile)[2])[0];
                        $zip->addFile($oggFile, "AudioClips".DIRECTORY_SEPARATOR.$result->getFileReference()."_".$oggIndex.".ogg");
                    }
                }
                $zip->close();

                $response = new Response(file_get_contents($zipLocation.$zipName));
                $response->headers->set('Content-Type', 'application/zip');
                $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
                $response->headers->set('Content-length', filesize($zipLocation.$zipName));
            } catch(Exception $e) {
                $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 500, 'data' => []]);
                $response->headers->set('Access-Control-Allow-Origin', '*');
                return $response;
            }
        
            @unlink($zipLocation.$zipName);
        
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/promos", name="api.promos")
     */
    public function promos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository(Promo::class)->findBy(array('isVisible' => true), array('id' => 'DESC'), 2);
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$results) {
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            foreach($results as $result) {
                $oneResult = [];

                $oneResult['id'] = $result->getId();
                $oneResult['title'] = $result->getTitle();
                $oneResult['type'] = $result->getType();
                $oneResult['textColor'] = $result->getTextColor();
                $oneResult['color'] = $result->getColor();
                $oneResult['button']['type'] = $result->getButtonType();
                $oneResult['button']['data'] = $result->getButtonData();
                $oneResult['isVisible'] = $result->getIsVisible();
                $oneResult['image_path'] = $baseUrl."/uploads/promo/".$result->getImagePath();

                $data[] = $oneResult;
            }
    
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

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
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 404, 'data' => []]);
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
            $resultsSongs = $em->getRepository(Song::class)->findBy(array('uploader' => $result->getId()));

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
                $oneResult['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
                $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

                $data['songs'][] = $oneResult;
            }
    
            $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
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
            $oneResult['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data['songs'][] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
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
            $oneResult['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";
            $oneResult['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data['songs'][] = $oneResult;
        }

        $response = new JsonResponse(['version' => $this->apiVersion, 'status' => 200, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
