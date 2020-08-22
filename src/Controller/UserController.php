<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     * @Route("/user/{userId}", name="user.detail")
     */
    public function userDetail(Request $request, int $userId)
    {
        return $this->redirectToRoute('user.detail.charts', array('userId' => $userId));
    }

    /**
     * @Route("/user/{userId}/charts", name="user.detail.charts")
     */
    public function userDetailCharts(Request $request, int $userId)
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
        $resultReviews = $em->getRepository(SongReview::class)->findBy(array('user' => $resultUser->getId()), array('reviewDate' => 'DESC'));
        $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('isActive' => true, 'user' => $resultUser->getId()), array('submitDate' => 'DESC'));

        $data['user'] = $resultUser;
        $data['charts'] = $resultCharts;
        $data['reviews'] = $resultReviews;
        $data['spinPlays'] = $resultSpinPlays;

        return $this->render('user/detail-charts.html.twig', $data);
    }

    /**
     * @Route("/user/{userId}/reviews", name="user.detail.reviews")
     */
    public function userDetailReviews(Request $request, int $userId)
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
        $resultReviews = $em->getRepository(SongReview::class)->findBy(array('user' => $resultUser->getId()), array('reviewDate' => 'DESC'));
        $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('isActive' => true, 'user' => $resultUser->getId()), array('submitDate' => 'DESC'));

        $data['user'] = $resultUser;
        $data['charts'] = $resultCharts;
        $data['reviews'] = $resultReviews;
        $data['spinPlays'] = $resultSpinPlays;

        return $this->render('user/detail-reviews.html.twig', $data);
    }

    /**
     * @Route("/user/{userId}/spinplays", name="user.detail.spinplays")
     */
    public function userDetailSpinPlays(Request $request, int $userId)
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
        $resultReviews = $em->getRepository(SongReview::class)->findBy(array('user' => $resultUser->getId()), array('reviewDate' => 'DESC'));
        $resultSpinPlays = $em->getRepository(SongSpinPlay::class)->findBy(array('isActive' => true, 'user' => $resultUser->getId()), array('submitDate' => 'DESC'));

        $data['user'] = $resultUser;
        $data['charts'] = $resultCharts;
        $data['reviews'] = $resultReviews;
        $data['spinPlays'] = $resultSpinPlays;

        return $this->render('user/detail-spinplays.html.twig', $data);
    }
}
