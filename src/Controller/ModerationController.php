<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ModerationController extends AbstractController
{
    /**
     * @Route("/moderation", name="moderation.index")
     */
    public function index()
    {
        return $this->render('moderation/index.html.twig', [
            'controller_name' => 'ModerationController',
        ]);
    }
}
