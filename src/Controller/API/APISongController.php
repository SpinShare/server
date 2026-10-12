<?php

namespace App\Controller\API;

use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Song;
use App\Entity\SongReview;
use App\Entity\SongSpinPlay;

class APISongController extends AbstractController
{
    /**
     * @Route("/api/song/{idOrReference}", name="api.songs.detail")
     * @Route("/api/song/{idOrReference}/")
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
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            $result->setViews($result->getViews() + 1);
            $em->persist($result);
            $em->flush();

            // TODO: This is a quick fix for mp3 support, swap with proper mp3 flag later on
            $oggURL = $baseUrl."/uploads/audio/".$result->getFileReference()."_0.ogg";
            $mp3URL = $baseUrl."/uploads/audio/".$result->getFileReference()."_0.mp3";
            $oggHeaders = get_headers($oggURL);

            if(stripos($oggHeaders[0],"200 OK")) {
                $audioURL = $oggURL;
            } else {
                $audioURL = $mp3URL;
            }

            $data = $result->getJSON();
            $data['paths']['ogg'] = $audioURL;
            $data['paths']['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $data['paths']['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }

    /**
     * @Route("/api/song/{idOrReference}/reviews", name="api.songs.detail.reviews")
     * @Route("/api/song/{idOrReference}/reviews/")
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
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            $resultReviewAverage = $em->getRepository(SongReview::class)->getAveragebyID($resultSong->getId());
            $data['average'] = $resultReviewAverage;
            
            $resultReviews = $em->getRepository(SongReview::class)->findBy(array('song' => $resultSong), array('reviewDate' => 'DESC'));

            foreach($resultReviews as $review) {
                $data['reviews'][] = $review->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }

    /**
     * @Route("/api/song/{idOrReference}/playlists", name="api.songs.detail.playlists")
     * @Route("/api/song/{idOrReference}/playlists/")
     */
    public function songDetailPlaylists(Request $request, $idOrReference)
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
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            $resultPlaylists = $resultSong->getSongPlaylists();

            foreach($resultPlaylists as $result) {
                $playlist = $result->getJSON();

                $songsCount = count($playlist['songs']);
                $playlist['songs'] = $songsCount;
                unset($playlist['description']);

                $data[] = $playlist;
            }

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }

    /**
     * @Route("/api/song/{idOrReference}/spinplays", name="api.songs.detail.spinplays")
     * @Route("/api/song/{idOrReference}/spinplays/")
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
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('song' => $resultSong, 'isActive' => true), array('submitDate' => 'DESC'));

            foreach($resultSpinPlays as $spinPlay) {
                $data['spinPlays'][] = $spinPlay->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }

    /**
     * @Route("/api/song/{id}/download", name="api.songs.download")
     * @Route("/api/song/{id}/download/")
     */
    public function songDownload(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $id));

        if(!$result) {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
        } else {
            // DLC Check
            // TODO: Uncomment once Client 3 Ships
            /*
            if($result->getDLC() != null) {
                $dlcHashHeader = $request->headers->get('SPSH-DLC-Hash');
                $dlcHashResult = $em->getRepository(DLCHash::class)->findOneBy(['hash' => $dlcHashHeader]);

                if($dlcHashResult == null) {
                    return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
                }

                if($result->getDLC() != $dlcHashResult->getDLC()) {
                    return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
                }
            } */

            // Download
            $zipLocation = $this->getParameter('temp_path').DIRECTORY_SEPARATOR;
            $zipName = $result->getFileReference().".zip";

            try {
                $result->setDownloads($result->getDownloads() + 1);
                $em->persist($result);
                $em->flush();

                $coverFiles = glob($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$result->getFileReference().".png");
                $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$result->getFileReference()."_*.ogg");
                $mp3Files = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$result->getFileReference()."_*.mp3");

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
                if(count($mp3Files) > 0) {
                    foreach($mp3Files as $mp3File) {
                        $mp3Index = explode(".", explode("_", $mp3File)[2])[0];
                        $zip->addFile($mp3File, "AudioClips".DIRECTORY_SEPARATOR.$result->getFileReference()."_".$mp3Index.".mp3");
                    }
                }
                $zip->close();

                $response = new Response(file_get_contents($zipLocation.$zipName));
                $response->headers->set('Content-Type', 'application/zip');
                $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
                $response->headers->set('Content-length', filesize($zipLocation.$zipName));
            } catch(Exception $e) {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 500, 'data' => []]);
                return $response;
            }
        
            @unlink($zipLocation.$zipName);

            return $response;
        }
    }

    private const UPDATE_HASHES_MAX_CHARTS = 500;

    /**
     * @Route("/api/updateHashes", name="api.updateHashes", methods={"POST"})
     * @Route("/api/updateHashes/", methods={"POST"})
     */
    public function updateHashes(Request $request)
    {
        $charts = $this->getChartsFromBody($request);

        if($charts === null) {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 400, 'data' => []]);
        }

        $data = $this->findUpdateHashes($charts);

        return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
    }

    /**
     * @Route("/api/updateHashes/check", name="api.updateHashes.check", methods={"POST"})
     * @Route("/api/updateHashes/check/", methods={"POST"})
     */
    public function checkUpdateHashes(Request $request)
    {
        $charts = $this->getChartsFromBody($request);

        if($charts === null) {
            return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 400, 'data' => []]);
        }

        // Skip pairs without a chart or hash
        $pairs = array_filter($charts, function($pair) {
            return is_array($pair)
                && isset($pair['chart']) && is_scalar($pair['chart'])
                && isset($pair['updateHash']) && is_scalar($pair['updateHash']);
        });

        $chartsById = [];
        $chartsByReference = [];
        foreach($this->findUpdateHashes(array_column($pairs, 'chart')) as $chart) {
            $chartsById[$chart['id']] = $chart;
            $chartsByReference[$chart['fileReference']] = $chart;
        }

        $data = [];
        foreach($pairs as $pair) {
            if(is_numeric($pair['chart'])) {
                $chart = $chartsById[intval($pair['chart'])] ?? null;
            } else {
                $chart = $chartsByReference[(string) $pair['chart']] ?? null;
            }

            $data[] = [
                'chart' => $pair['chart'],
                'isUpToDate' => $chart !== null && hash_equals($chart['updateHash'] ?? '', (string) $pair['updateHash']),
            ];
        }

        return new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
    }

    /**
     * Returns the "charts" array of the JSON body, or null if it is missing, empty or too large.
     */
    private function getChartsFromBody(Request $request): ?array
    {
        $jsonBody = json_decode($request->getContent(), true);

        if(!is_array($jsonBody) || !isset($jsonBody['charts']) || !is_array($jsonBody['charts'])) {
            return null;
        }

        $charts = array_values($jsonBody['charts']);

        if(count($charts) == 0 || count($charts) > self::UPDATE_HASHES_MAX_CHARTS) {
            return null;
        }

        return $charts;
    }

    /**
     * Looks up id, fileReference and updateHash for a list of chart IDs and/or file references.
     */
    private function findUpdateHashes(array $idsOrReferences): array
    {
        $chartIds = [];
        $chartReferences = [];

        foreach($idsOrReferences as $idOrReference) {
            if(!is_scalar($idOrReference) || $idOrReference === '') {
                continue;
            }

            if(is_numeric($idOrReference)) {
                $chartIds[] = intval($idOrReference);
            } else {
                $chartReferences[] = (string) $idOrReference;
            }
        }

        $chartIds = array_values(array_unique($chartIds));
        $chartReferences = array_values(array_unique($chartReferences));

        if(count($chartIds) == 0 && count($chartReferences) == 0) {
            return [];
        }

        $em = $this->getDoctrine()->getManager();
        $qb = $em->getRepository(Song::class)->createQueryBuilder('s')
                                             ->select('s.id, s.fileReference, s.updateHash');

        if(count($chartIds) > 0) {
            $qb->orWhere('s.id IN (:ids)')->setParameter('ids', $chartIds);
        }
        if(count($chartReferences) > 0) {
            $qb->orWhere('s.fileReference IN (:references)')->setParameter('references', $chartReferences);
        }

        return $qb->getQuery()->getArrayResult();
    }
}
