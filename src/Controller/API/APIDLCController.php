<?php

namespace App\Controller\API;

use App\Entity\DLC;
use App\Entity\DLCHash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class APIDLCController extends AbstractController
{
    /**
     * @Route("/api/dlc", name="api.dlc")
     */
    public function allDlcs(): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $dlcs = $em->getRepository(DLC::class)->findAll();

        foreach($dlcs as $dlc) {
            $data[] = $dlc->getJSON();
        }

        return new JsonResponse([
            'version' => $this->getParameter('api_version'),
            'status' => 200,
            'data' => $data
        ]);
    }
    /**
     * @Route("/api/dlc/verify/{hash}", name="api.dlc.verify")
     * @Route("/api/dlc/verify/{hash}/")
     */
    public function verifyDLC(string $hash): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();

        $dlcHash = $em->getRepository(DLCHash::class)->findOneBy([
            'hash' => $hash
        ]);

        if ($dlcHash != null) {
            return new JsonResponse([
                'version' => $this->getParameter('api_version'),
                'status' => 200,
                'data' => $dlcHash->getDLC()->getJSON()
            ]);
        } else {
            return new JsonResponse([
                'version' => $this->getParameter('api_version'),
                'status' => 404,
                'data' => null
            ]);
        }
    }
}
