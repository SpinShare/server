<?php

namespace App\Controller\API;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class APIDocsController extends AbstractController
{
    /**
     * @Route("/api/docs", name="api.docs.introduction")
     */
    public function introduction(): Response
    {
        return $this->render('apidocs/gettingstarted/introduction.html.twig');
    }

    /**
     * @Route("/api/docs/usage-policy", name="api.docs.usagePolicy")
     */
    public function usagePolicy(): Response
    {
        return $this->render('apidocs/gettingstarted/usage-policy.html.twig');
    }

    /**
     * @Route("/api/docs/endpoints-versioning", name="api.docs.endpointsVersioning")
     */
    public function endpointsVersioning(): Response
    {
        return $this->render('apidocs/gettingstarted/endpoints-versioning.html.twig');
    }

    /**
     * @Route("/api/docs/authentication", name="api.docs.authentication")
     */
    public function authentication(): Response
    {
        return $this->render('apidocs/gettingstarted/authentication.html.twig');
    }

    /**
     * @Route("/api/docs/open/discovery", name="api.docs.open.discovery")
     */
    public function openDiscovery(): Response
    {
        return $this->render('apidocs/open/discovery.html.twig');
    }

    /**
     * @Route("/api/docs/open/client", name="api.docs.open.client")
     */
    public function openClient(): Response
    {
        return $this->render('apidocs/open/client.html.twig');
    }

    /**
     * @Route("/api/docs/open/promos", name="api.docs.open.promos")
     */
    public function openPromos(): Response
    {
        return $this->render('apidocs/open/promos.html.twig');
    }

    /**
     * @Route("/api/docs/open/songs", name="api.docs.open.songs")
     */
    public function openSongs(): Response
    {
        return $this->render('apidocs/open/songs.html.twig');
    }

    /**
     * @Route("/api/docs/open/playlists", name="api.docs.open.playlists")
     */
    public function openPlaylists(): Response
    {
        return $this->render('apidocs/open/playlists.html.twig');
    }

    /**
     * @Route("/api/docs/open/users", name="api.docs.open.users")
     */
    public function openUsers(): Response
    {
        return $this->render('apidocs/open/users.html.twig');
    }

    /**
     * @Route("/api/docs/open/tournaments", name="api.docs.open.tournaments")
     */
    public function openTournaments(): Response
    {
        return $this->render('apidocs/open/tournaments.html.twig');
    }

    /**
     * @Route("/api/docs/open/dlcs", name="api.docs.open.dlcs")
     */
    public function openDLCs(): Response
    {
        return $this->render('apidocs/open/dlcs.html.twig');
    }

    /**
     * @Route("/api/docs/connect/connect", name="api.docs.connect.connect")
     */
    public function connectConnect(): Response
    {
        return $this->render('apidocs/connect/connect.html.twig');
    }

    /**
     * @Route("/api/docs/connect/profile", name="api.docs.connect.profile")
     */
    public function connectProfile(): Response
    {
        return $this->render('apidocs/connect/profile.html.twig');
    }

    /**
     * @Route("/api/docs/connect/playlists", name="api.docs.connect.playlists")
     */
    public function connectPlaylists(): Response
    {
        return $this->render('apidocs/connect/playlists.html.twig');
    }

    /**
     * @Route("/api/docs/connect/notifications", name="api.docs.connect.notifications")
     */
    public function connectNotifications(): Response
    {
        return $this->render('apidocs/connect/notifications.html.twig');
    }

    /**
     * @Route("/api/docs/connect/reviews", name="api.docs.connect.reviews")
     */
    public function connectReviews(): Response
    {
        return $this->render('apidocs/connect/reviews.html.twig');
    }
}