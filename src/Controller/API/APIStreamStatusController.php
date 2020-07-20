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

class APIStreamStatusController extends AbstractController
{
    /**
     * @Route("/api/streamStatus", name="api.streamStatus")
     */
    public function streamStatus()
    {
        $data = [];
        $successful = false;

        try {
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

            $successful = true;
            
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
        } catch(\Exception $e) {
            $successful = false;
            $data = [];
        }

        $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => $successful ? 200 : 500, 'data' => $data]);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
