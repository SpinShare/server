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
        $data['promos'] = $activePromos;
        
        /* $newOffset = $request->query->get('newOffset');
        $popularOffset = $request->query->get('popularOffset');

        $resultsNewSongs = $em->getRepository(Song::class)->findBy(array(), array('id' => 'DESC'), 6, $newOffset * 6);
        $resultsPopularSongs = $em->getRepository(Song::class)->findBy(array(), array('downloads' => 'DESC', 'views' => 'DESC'), 6, $popularOffset * 6);

        $data['newSongs'] = $resultsNewSongs;
        $data['newOffset'] = $newOffset;
        $data['popularSongs'] = $resultsPopularSongs;
        $data['popularOffset'] = $popularOffset; */

        return $this->render('index/index.html.twig', $data);
    }

    /**
     * @Route("/new", name="index.new")
     */
    public function new(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        
        $newOffset = $request->query->get('newOffset') ? $request->query->get('newOffset') : 0;
        $resultsNewSongs = $em->getRepository(Song::class)->getNew($newOffset);

        $data['newSongs'] = $resultsNewSongs;
        $data['newOffset'] = $newOffset;

        return $this->render('index/new.html.twig', $data);
    }

    /**
     * @Route("/hot", name="index.hot")
     */
    public function hot(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        
        $hotOffset = $request->query->get('hotOffset') ? $request->query->get('hotOffset') : 0;
        $resultsHotSongs = $em->getRepository(Song::class)->getHot($hotOffset);

        $data['hotSongs'] = $resultsHotSongs;
        $data['hotOffset'] = $hotOffset;

        return $this->render('index/hot.html.twig', $data);
    }

    /**
     * @Route("/popular", name="index.popular")
     */
    public function popular(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        
        $popularOffset = $request->query->get('popularOffset') ? $request->query->get('popularOffset') : 0;
        $resultsPopularSongs = $em->getRepository(Song::class)->getPopular($popularOffset);

        $data['popularSongs'] = $resultsPopularSongs;
        $data['popularOffset'] = $popularOffset;

        return $this->render('index/popular.html.twig', $data);
    }

    /**
     * @Route("/support", name="index.support")
     */
    public function support(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $patreons = $em->getRepository(User::class)->findBy(array('isPatreon' => true), array('id' => 'DESC'));
        $data['patreons'] = $patreons;

        return $this->render('index/support.html.twig', $data);
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
        $getLatestMac = $em->getRepository(ClientRelease::class)->findOneBy(array('platform' => 'darwin'), array('majorVersion' => 'DESC', 'minorVersion' => 'DESC', 'patchVersion' => 'DESC'));

        $data['latestWindows'] = $getLatestWindows;
        $data['latestMac'] = $getLatestMac;

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
