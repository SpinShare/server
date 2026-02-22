<?php

namespace App\Controller\Moderation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ModerationController extends AbstractController
{
    /**
     * @Route("/moderation", name="moderation.index")
     */
    public function index(Request $request)
    {
        return $this->redirectToRoute('moderation.reports.user.index');
    }
}
