<?php

namespace App\Controller\API\Connect;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\User;
use App\Entity\Connection;
use App\Entity\ConnectApp;
use App\Entity\Song;
use App\Entity\SongPlaylist;
use App\Entity\UserNotification;

class APIConnectPlaylistController extends AbstractController
{
    /**
     * @Route("/api/connect/playlists", name="api.connect.playlists")
     * @Route("/api/connect/playlists/")
     */
    public function getPlaylists(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        if($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            foreach($connection->getUser()->getSongPlaylists() as $playlist) {
                $data[] = $playlist->getJSON();
            }

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }
    
    /**
     * @Route("/api/connect/savePlaylists/{songID}/", name="api.connect.savePlaylists")
     * @Route("/api/connect/savePlaylists/{songID}")
     */
    public function savePlaylists(Request $request, int $songID)
    {
        // TODO: DOCUMENTATION & FIX

        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        //$jsonBody = json_decode($request->getContent(), true);

        if($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            //$playlistsToSaveTo = $jsonBody['playlists'];

            /*
            *
            *       FUCK THIS, THIS IS BROKEN, WTF WHY IS IT RECURSIVE???
            *
            */

            $song = $em->getRepository(Song::class)->findOneBy(array('id' => $songID));
            
            var_dump($song->getSongPlaylists());

            // Remove all playlist Entries
            //$playlistsByUser = $em->getRepository(SongPlaylist::class)->findBy(array('user' => $connection->getUser()));
            
            // Add to all playlists
            //var_dump($playlistsByUser);

            //var_dump($playlistsToSaveTo);

            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        } else {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }
}
