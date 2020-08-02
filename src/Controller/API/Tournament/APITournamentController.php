<?php

namespace App\Controller\API\Tournament;

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
    public function tournamentMappool()
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        // Botch
        $tournamentCharts = $em->getRepository(Song::class)->findBy(array('isTournament' => true));

        foreach($tournamentCharts as $tournamentChart) {
            $chartItem = $tournamentChart->getJSON();

            $chartItem['srtbMD5'] = md5_file($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$tournamentChart->getFileReference().".srtb");

            $data[] = $chartItem;
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
