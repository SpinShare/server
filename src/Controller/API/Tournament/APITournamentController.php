<?php

namespace App\Controller\API\Tournament;

use App\Entity\SongPlaylist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\Song;

class APITournamentController extends AbstractController
{
    /**
     * @Route("/api/tournament/mappool", name="api.tournament.mappool")
     */
    public function tournamentMappool(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        // Botch
        $tournamentPlaylist = $em->getRepository(SongPlaylist::class)->findOneBy(array('id' => "408"));

        foreach($tournamentPlaylist->getSongs() as $tournamentChart) {
            $chartItem = $tournamentChart->getJSON();

            $chartItem['srtbMD5'] = md5_file($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$tournamentChart->getFileReference().".srtb");
            $chartItem['paths']['ogg'] = $baseUrl."/uploads/audio/".$tournamentChart->getFileReference()."_0.ogg";
            $chartItem['paths']['cover'] = $baseUrl."/uploads/thumbnail/".$tournamentChart->getFileReference().".jpg";
            $chartItem['paths']['zip'] = $this->generateUrl('api.songs.download', array('id' => $tournamentChart->getId()), UrlGeneratorInterface::ABSOLUTE_URL);

            $data[] = $chartItem;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        return $response;
    }
}
