<?php

namespace App\Controller\Moderation;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\ClientRelease;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\SongReport;
use App\Entity\UserReport;
use App\Entity\Promo;

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
