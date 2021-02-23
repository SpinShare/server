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

class APIPromosController extends AbstractController
{
    /**
     * @Route("/api/promos", name="api.promos")
     * @Route("/api/promos/")
     */
    public function promos(Request $request)
    {
        // TODO: DEPRECATION

        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository(Promo::class)->findBy(array('isVisible' => true), array('id' => 'DESC'), 2);
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$results) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            foreach($results as $result) {
                $data[] = $result->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }

    /**
     * @Route("/api/activePromos", name="api.activePromos")
     * @Route("/api/activePromos/")
     */
    public function activePromos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository(Promo::class)->findBy(array('isVisible' => true), array('id' => 'DESC'));
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$results) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            return $response;
        } else {
            foreach($results as $result) {
                $data[] = $result->getJSON();
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            return $response;
        }
    }
}
