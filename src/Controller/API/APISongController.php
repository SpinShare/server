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
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $result->setViews($result->getViews() + 1);
            $em->persist($result);
            $em->flush();

            $data = $result->getJSON();
            $data['paths']['ogg'] = $baseUrl."/uploads/audio/".$result->getFileReference()."_0.ogg";
            $data['paths']['cover'] = $baseUrl."/uploads/thumbnail/".$result->getFileReference().".jpg";
            $data['paths']['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
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
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $resultReviewAverage = $em->getRepository(SongReview::class)->getAveragebyID($resultSong->getId());
            $data['average'] = $resultReviewAverage;
            
            $resultReviews = $em->getRepository(SongReview::class)->findBy(array('song' => $resultSong), array('reviewDate' => 'DESC'));

            foreach($resultReviews as $review) {
                $data['reviews'][] = $review->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
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
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('song' => $resultSong, 'isActive' => true), array('submitDate' => 'DESC'));

            foreach($resultSpinPlays as $spinPlay) {
                $data['spinPlays'][] = $spinPlay->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }

    /**
     * @Route("/api/song/{id}/download", name="api.songs.download")
     * @Route("/api/song/{id}/download/")
     */
    public function songDownload(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $id));

        $zipLocation = $this->getParameter('temp_path').DIRECTORY_SEPARATOR;
        $zipName = $result->getFileReference().".zip";

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            try {
                $result->setDownloads($result->getDownloads() + 1);
                $em->persist($result);
                $em->flush();

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
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 500, 'data' => []]);
                $response->headers->set('Access-Control-Allow-Origin', '*');
                return $response;
            }
        
            @unlink($zipLocation.$zipName);
        
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
