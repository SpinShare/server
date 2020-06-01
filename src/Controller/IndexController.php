<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Entity\ClientRelease;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Promo;

class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index.index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $activePromos = $em->getRepository(Promo::class)->findBy(array('isVisible' => true), array('id' => 'DESC'), 2);
        
        $newOffset = $request->query->get('newOffset');
        $popularOffset = $request->query->get('popularOffset');

        $resultsNewSongs = $em->getRepository(Song::class)->findBy(array(), array('id' => 'DESC'), 6, $newOffset * 6);
        $resultsPopularSongs = $em->getRepository(Song::class)->findBy(array(), array('downloads' => 'DESC', 'views' => 'DESC'), 6, $popularOffset * 6);

        $data['promos'] = $activePromos;
        $data['newSongs'] = $resultsNewSongs;
        $data['newOffset'] = $newOffset;
        $data['popularSongs'] = $resultsPopularSongs;
        $data['popularOffset'] = $popularOffset;

        return $this->render('index/index.html.twig', $data);
    }

    /**
     * @Route("/support", name="index.support")
     */
    public function support(Request $request)
    {
        return $this->redirect('https://patreon.com/spinshare');
/*
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $patreons = $em->getRepository(User::class)->findBy(array('isPatreon' => true), array('id' => 'DESC'));
        $data['patreons'] = $patreons;

        return $this->render('index/support.html.twig', $data); */
    }

    /**
     * @Route("/discord", name="index.discord")
     */
    public function discord(Request $request)
    {
        return $this->redirect('https://discord.gg/j8g2gkF');
    }

    /**
     * @Route("/client", name="index.client")
     */
    public function client(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $getLatestWindows = $em->getRepository(ClientRelease::class)->findOneBy(array('platform' => 'win32'), array('majorVersion' => 'DESC', 'minorVersion' => 'DESC', 'patchVersion' => 'DESC'));
        // $getLatestMac = $em->getRepository(ClientRelease::class)->findOneBy(array('platform' => 'mac'), array('majorVersion' => 'DESC', 'minorVersion' => 'DESC', 'patchVersion' => 'DESC'));

        $data['latestWindows'] = $getLatestWindows->getFileReference();
        // $data['latestMac'] = $getLatestMac->getFileReference();

        return $this->render('index/client.html.twig', $data);
    }

    /**
     * @Route("/legal", name="index.legal")
     */
    public function legal(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('index/legal.html.twig', $data);
    }
}
