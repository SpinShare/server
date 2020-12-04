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
        $em = $this->getDoctrine()->getManager();

        $results = $em->getRepository(Promo::class)->findBy(array('isVisible' => true), array('id' => 'DESC'), 2);
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        if(!$results) {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        } else {
            foreach($results as $result) {
                $oneResult = [];

                $oneResult['id'] = $result->getId();
                $oneResult['title'] = $result->getTitle();
                $oneResult['type'] = $result->getType();
                $oneResult['textColor'] = $result->getTextColor();
                $oneResult['color'] = $result->getColor();
                $oneResult['button']['type'] = $result->getButtonType();
                $oneResult['button']['data'] = $result->getButtonData();
                $oneResult['isVisible'] = $result->getIsVisible();
                $oneResult['image_path'] = $baseUrl."/uploads/promo/".$result->getImagePath();

                $data[] = $oneResult;
            }
    
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $data]);
            $response->headers->set('Access-Control-Allow-Origin', '*');
            return $response;
        }
    }
}
