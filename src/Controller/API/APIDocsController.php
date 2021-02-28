<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class APIDocsController extends AbstractController
{
    /**
     * @Route("/api/docs", name="api.docs.introduction")
     */
    public function introduction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/gettingstarted/introduction.html.twig', $data);
    }

    /**
     * @Route("/api/docs/usage-policy", name="api.docs.usagePolicy")
     */
    public function usagePolicy(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/gettingstarted/usage-policy.html.twig', $data);
    }

    /**
     * @Route("/api/docs/endpoints-versioning", name="api.docs.endpointsVersioning")
     */
    public function endpointsVersioning(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/gettingstarted/endpoints-versioning.html.twig', $data);
    }

    /**
     * @Route("/api/docs/authentication", name="api.docs.authentication")
     */
    public function authentication(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/gettingstarted/authentication.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/discovery", name="api.docs.open.discovery")
     */
    public function openDiscovery(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/discovery.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/client", name="api.docs.open.client")
     */
    public function openClient(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/client.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/promos", name="api.docs.open.promos")
     */
    public function openPromos(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/promos.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/songs", name="api.docs.open.songs")
     */
    public function openSongs(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/songs.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/playlists", name="api.docs.open.playlists")
     */
    public function openPlaylists(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/playlists.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/users", name="api.docs.open.users")
     */
    public function openUsers(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/users.html.twig', $data);
    }

    /**
     * @Route("/api/docs/open/tournaments", name="api.docs.open.tournaments")
     */
    public function openTournaments(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/open/tournaments.html.twig', $data);
    }

    /**
     * @Route("/api/docs/connect/connect", name="api.docs.connect.connect")
     */
    public function connectConnect(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/connect/connect.html.twig', $data);
    }

    /**
     * @Route("/api/docs/connect/profile", name="api.docs.connect.profile")
     */
    public function connectProfile(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/connect/profile.html.twig', $data);
    }

    /**
     * @Route("/api/docs/connect/playlists", name="api.docs.connect.playlists")
     */
    public function connectPlaylists(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/connect/playlists.html.twig', $data);
    }

    /**
     * @Route("/api/docs/connect/notifications", name="api.docs.connect.notifications")
     */
    public function connectNotifications(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/connect/notifications.html.twig', $data);
    }

    /**
     * @Route("/api/docs/connect/reviews", name="api.docs.connect.reviews")
     */
    public function connectReviews(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('apidocs/connect/reviews.html.twig', $data);
    }
}
