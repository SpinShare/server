<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Entity\Song;

class APIController extends AbstractController
{
    public $currentVersion = 1;

    /**
     * @Route("/api/ping", name="api.ping")
     */
    public function ping()
    {
        return new JsonResponse(['version' => $this->currentVersion, 'status' => 200, 'pong' => true]);
    }

    /**
     * @Route("/api/songs/new", name="api.songs.new")
     */
    public function songsNew()
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $results = $em->getRepository(Song::class)->findBy(array(), array('id' => 'DESC'), 10, 0);

        foreach($results as $result) {
            $data['songs'][] = $result->getId();
        }

        return new JsonResponse(['version' => $this->currentVersion, 'status' => 200, 'data' => $data]);
    }

    /**
     * @Route("/api/songs/hot", name="api.songs.hot")
     */
    public function songsHot()
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return new JsonResponse(['version' => $this->currentVersion, 'status' => 200, 'data' => $data]);
    }

    /**
     * @Route("/api/song/{id}", name="api.songs.detail")
     */
    public function songDetail(Request $request, int $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $id));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            return new JsonResponse(['version' => $this->currentVersion, 'status' => 404, 'data' => []]);
        } else {
            $data['title'] = $result->getTitle();
            $data['subtitle'] = $result->getTitle();
            $data['artist'] = $result->getArtist();
            $data['charter'] = $result->getCharter();
            $data['paths']['ogg'] = $baseUrl.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."audio".DIRECTORY_SEPARATOR.$result->getFileReference().".ogg";
            $data['paths']['cover'] = $baseUrl.DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."cover".DIRECTORY_SEPARATOR.$result->getFileReference().".png";
            $data['paths']['zip'] = $this->generateUrl('api.songs.download', array('id' => $result->getId()), UrlGeneratorInterface::ABSOLUTE_URL);
    
            return new JsonResponse(['version' => $this->currentVersion, 'status' => 200, 'data' => $data]);
        }
    }

    /**
     * @Route("/api/song/{id}/download", name="api.songs.download")
     */
    public function songDownload(int $id)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $id));

        if(!$result) {
            return new JsonResponse(['version' => $this->currentVersion, 'status' => 404, 'data' => []]);
        } else {
            $zipLocation = $this->getParameter('temp_path').DIRECTORY_SEPARATOR;
            $zipName = $result->getSRTBOriginalName().".zip";

            $zip = new \ZipArchive;
            $zip->open($zipLocation.$zipName, \ZipArchive::CREATE);
            $zip->addFile($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$result->getFileReference().".srtb", $result->getSRTBOriginalName());
            $zip->addFile($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$result->getFileReference().".png", "AlbumArt".DIRECTORY_SEPARATOR.$result->getCoverOriginalName());
            $zip->addFile($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$result->getFileReference().".ogg", "AudioClips".DIRECTORY_SEPARATOR.$result->getAudioOriginalName());
            $zip->close();

            $response = new Response(file_get_contents($zipLocation.$zipName));
            $response->headers->set('Content-Type', 'application/zip');
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
            $response->headers->set('Content-length', filesize($zipLocation.$zipName));
        
            @unlink($zipLocation.$zipName);
        
            return $response;
        }
    }
}
