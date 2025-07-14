<?php

namespace App\Controller\API\Connect;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpClient\HttpClient;

use App\Entity\User;
use App\Entity\Song;
use App\Entity\SongReview;
use App\Entity\Connection;
use App\Entity\ConnectApp;
use App\Entity\UserNotification;

class APIConnectReviewsController extends AbstractController
{

    /**
     * @Route("/api/connect/reviews/{songID}/get", name="api.connect.reviews.get")
     * @Route("/api/connect/reviews/{songID}/get/")
     */
    public function getReview(Request $request, $songID)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        // 422 - Parameter Missing
        if($connectToken == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 422, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            // Find Song
            $songToReview = $em->getRepository(Song::class)->findOneBy(array('id' => $songID));

            // 404 - Song not Found
            if(!$songToReview) {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
                return $response;
            }

            $previousReview = $em->getRepository(SongReview::class)->findOneBy(array('song' => $songToReview, 'user' => $connection->getUser()));
            
            if($previousReview) {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => $previousReview->getJSON()]);
                return $response;
            } else {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
                return $response;
            }
        } else {
            // 403 - Not Authenticated
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }

    /**
     * @Route("/api/connect/reviews/{songID}/add", name="api.connect.reviews.add")
     * @Route("/api/connect/reviews/{songID}/add/")
     */
    public function addReview(Request $request, $songID)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $connectToken = $request->query->get('connectToken');

        $reviewRecommend = $request->request->get('recommend');
        $reviewComment = $request->request->get('comment');

        // 422 - Parameter Missing
        if($connectToken == "" || $reviewRecommend == "" || $songID == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 422, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            // Find Song
            $songToReview = $em->getRepository(Song::class)->findOneBy(array('id' => $songID));
            $songUploader = $em->getRepository(User::class)->findOneBy(array('id' => $songToReview->getUploader()));

            // 404 - Song not Found
            if(!$songToReview) {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
                return $response;
            }

            $previousReview = $em->getRepository(SongReview::class)->findOneBy(array('song' => $songToReview, 'user' => $connection->getUser()));
            
            if($previousReview) {
                // Update Existing Review
                $previousReview->setRecommended($reviewRecommend == "true" | $reviewRecommend == "1" ? true : false);
                if($reviewComment != "") { $previousReview->setComment($reviewComment); }
                $previousReview->setReviewDate(new \DateTime('NOW'));

                $em->persist($previousReview);
                $em->flush();

                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
                return $response;
            } else {
                // Create new Review
                $newReview = new SongReview();
                $newReview->setUser($connection->getUser());
                $newReview->setSong($songToReview);
                $newReview->setRecommended($reviewRecommend == "true" || $reviewRecommend == "1" ? true : false);
                $newReview->setComment($reviewComment);
                $newReview->setReviewDate(new \DateTime('NOW'));

                $em->persist($newReview);
                $em->flush();

                // Add Notification
                $newNotification = new UserNotification();
                $newNotification->setUser($songUploader);
                $newNotification->setConnectedUser($connection->getUser());
                $newNotification->setConnectedSong($songToReview);
                $newNotification->setNotificationType(1);
                $newNotification->setNotificationData($newReview->getID());
                $em->persist($newNotification);
                $em->flush();

                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 201, 'data' => []]);
                return $response;
            }
        } else {
            // 403 - Not Authenticated
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }

    /**
     * @Route("/api/connect/reviews/{songID}/remove", name="api.connect.reviews.remove")
     * @Route("/api/connect/reviews/{songID}/remove/")
     */
    public function removeReview(Request $request, $songID)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $connectToken = $request->query->get('connectToken');

        // 422 - Parameter Missing
        if($connectToken == "" || $songID == "") {
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 422, 'data' => []]);
            return $response;
        }

        $connection = $em->getRepository(Connection::class)->findOneBy(array('connectToken' => $connectToken));

        if($connection) {
            // Find Song and Review
            $song = $em->getRepository(Song::class)->findOneBy(array('id' => $songID));
            $review = $em->getRepository(SongReview::class)->findOneBy(array(
                'song' => $song,
                'user' => $connection->getUser()
            ));

            // 404 - Song or Review not Found
            if(!$song || !$review) {
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 404, 'data' => []]);
                return $response;
            }

            // Check if user owns the review or has admin/moderator roles
            $userRoles = $connection->getUser()->getRoles();
            $allowedRoles = ["ROLE_ADMIN", "ROLE_SUPERADMIN", "ROLE_MODERATOR"];

            if(count(array_intersect($allowedRoles, $userRoles)) > 0 || $review->getUser() == $connection->getUser()) {
                $em->remove($review);
                $em->flush();

                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 200, 'data' => []]);
                return $response;
            } else {
                // 403 - Not Authorized to remove this review
                $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
                return $response;
            }
        } else {
            // 403 - Not Authenticated
            $response = new JsonResponse(['version' => $this->getParameter('api_version'), 'status' => 403, 'data' => []]);
            return $response;
        }
    }
}
