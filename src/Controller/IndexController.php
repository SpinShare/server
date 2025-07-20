<?php

namespace App\Controller;

use App\Entity\SongPlaylist;
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

        // 144 = Featured Playlist
        $resultPlaylist = $em->getRepository(SongPlaylist::class)->findOneBy(array('id' => 144));
        $data['featuredSongs'] = $resultPlaylist->getSongs()->slice(0, 10);

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
     * @Route("/updated", name="index.updated")
     */
    public function updated(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        
        $updatedOffset = $request->query->get('updatedOffset') ? $request->query->get('updatedOffset') : 0;
        $resultsUpdatedSongs = $em->getRepository(Song::class)->getUpdated($updatedOffset);

        $data['updatedSongs'] = $resultsUpdatedSongs;
        $data['updatedOffset'] = $updatedOffset;

        return $this->render('index/updated.html.twig', $data);
    }

    /**
     * @Route("/hotThisWeek", name="index.hotThisWeek")
     */
    public function hotThisWeek(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        
        $hotWeekOffset = $request->query->get('hotWeekOffset') ? $request->query->get('hotWeekOffset') : 0;
        $resultsHotWeekSongs = $em->getRepository(Song::class)->getHotThisWeek($hotWeekOffset);

        $data['hotWeekSongs'] = $resultsHotWeekSongs;
        $data['hotWeekOffset'] = $hotWeekOffset;

        return $this->render('index/hotThisWeek.html.twig', $data);
    }

    /**
     * @Route("/hotThisMonth", name="index.hotThisMonth")
     */
    public function hotThisMonth(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        
        $hotMonthOffset = $request->query->get('hotMonthOffset') ? $request->query->get('hotMonthOffset') : 0;
        $resultsHotMonthSongs = $em->getRepository(Song::class)->getHotThisMonth($hotMonthOffset);

        $data['hotMonthSongs'] = $resultsHotMonthSongs;
        $data['hotMonthOffset'] = $hotMonthOffset;

        return $this->render('index/hotThisMonth.html.twig', $data);
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
     * @Route("/client-next", name="index.clientNext")
     */
    public function clientNext(Request $request)
    {

        return $this->render('index/client-next.html.twig');
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
