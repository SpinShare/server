<?php

namespace App\Controller;

use App\Entity\SongPlaylist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Entity\User;
use App\Entity\Song;
use App\Entity\SongReview;
use App\Entity\SongSpinPlay;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{userId}/{area}", defaults={"area"="charts"}, name="user.detail")
     * @param Request $request
     * @param int $userId
     * @return Response
     */
    public function userDetailCharts(Request $request, int $userId, string $area)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if($user) {
            if($user->getId() == $userId) {
                if($request->request->get('save') == "changeAvatar") {
                    if($request->files->get('newAvatar')) {
                        $allowedMimeTypes = array("image/jpeg", "image/png");

                        if(in_array($request->files->get('newAvatar')->getMimeType(), $allowedMimeTypes)) {
                            @unlink($this->getParameter('avatar_path').DIRECTORY_SEPARATOR.$user->getCoverReference());

                            $user->setCoverReference(uniqid().".png");
                            rename($request->files->get('newAvatar'), $this->getParameter('avatar_path').DIRECTORY_SEPARATOR.$user->getCoverReference());

                            $em->persist($user);
                            $em->flush();
                        }
                    }
                }
            }
        }

        $resultUser = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        if(!$resultUser) throw new NotFoundHttpException();

        $resultCharts = $em->getRepository(Song::class)->findBy(array('uploader' => $resultUser->getId()), array('uploadDate' => 'DESC'));
        $resultPlaylists = $em->getRepository(SongPlaylist::class)->findBy(array('user' => $resultUser->getId()), array('id' => 'DESC'));
        $resultReviews = $em->getRepository(SongReview::class)->findBy(array('user' => $resultUser->getId()), array('reviewDate' => 'DESC'));
        $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('isActive' => true, 'user' => $resultUser->getId()), array('submitDate' => 'DESC'));

        $data['user'] = $resultUser;
        $data['area'] = $area;
        $data['charts'] = $resultCharts;
        $data['playlists'] = $resultPlaylists;
        $data['reviews'] = $resultReviews;
        $data['spinPlays'] = $resultSpinPlays;

        switch($area) {
            case "charts":
                return $this->render('user/detail-charts.html.twig', $data);
            case "playlists":
                return $this->render('user/detail-playlists.html.twig', $data);
            case "reviews":
                return $this->render('user/detail-reviews.html.twig', $data);
            case "spinplays":
                return $this->render('user/detail-spinplays.html.twig', $data);
        }

        return null;
    }
}
