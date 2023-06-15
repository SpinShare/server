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
use App\Entity\SongPlaylist;
use App\Entity\User;
use App\Entity\Promo;

class APISongPlaylistController extends AbstractController
{
    /**
     * @Route("/api/playlist/{id}", name="api.playlist.detail")
     * @Route("/api/playlist/{id}/")
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function playlistDetail(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(SongPlaylist::class)->findOneBy(array('id' => $id));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            $data = $result->getJSON();
            $data['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";

            // Sort Newest First
            usort($data['songs'], function ($a, $b) {
                return $a['id'] < $b['id'];
            });

            // Add needed paths for display
            foreach($data['songs'] as $songKey => $songItem) {
                $songItem['cover'] = $baseUrl."/uploads/thumbnail/".$songItem['fileReference'].".jpg";
                $songItem['zip'] = $this->generateUrl('api.songs.download', array('id' => $songItem['id']), UrlGeneratorInterface::ABSOLUTE_URL);

                // TODO: Remove this (Botched for SSSO)
                $songItem['currentVersion'] = md5_file($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$songItem['fileReference'].".srtb");

                $data['songs'][$songKey] = $songItem;
            }

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }
}
