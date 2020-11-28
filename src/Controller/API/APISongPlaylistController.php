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
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function playlistDetail(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(SongPlaylist::class)->findOneBy(array('id' => $id, 'publicationStatus' => array(0, 1, 2)));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$result) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            $data = $result->getJSON();
            $data['paths']['cover'] = $baseUrl."/uploads/cover/".$result->getFileReference().".png";

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
