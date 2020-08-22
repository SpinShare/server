<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

use App\Entity\UserNotification;
use App\Entity\Song;
use App\Entity\User;
use App\Entity\Promo;

class NotificationController extends AbstractController
{
    /**
     * @Route("/notification/clear/{notificationID}", name="notification.clear")
     */
    public function clearNotification(Request $request, $notificationID)
    {
        $em = $this->getDoctrine()->getManager();

        $notificationToClear = $em->getRepository(UserNotification::class)->findOneBy(array('id' => $notificationID, 'user' => $this->getUser()));

        if($notificationToClear != null) {
            $em->remove($notificationToClear);
            $em->flush();

            switch($notificationToClear->getNotificationType()) {
                default:
                case 0:
                    // System
                    $returnUrl = $request->query->get('returnUrl');
                    return $this->redirect($returnUrl);
                break;
                case 1:
                    // Review
                    return $this->redirectToRoute('song.detail', ['songId' => $notificationToClear->getConnectedSong()->getId(), 'tab' => 'reviews']);
                break;
                case 2:
                    // SpinPlay
                    return $this->redirectToRoute('song.detail', ['songId' => $notificationToClear->getConnectedSong()->getId(), 'tab' => 'spinplays']);
                break;
            }
        } else {
            $returnUrl = $request->query->get('returnUrl');
            return $this->redirect($returnUrl);
        }
    }
    /**
     * @Route("/notification/clearAll", name="notification.clear.all")
     */
    public function clearAllNotifications(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $notificationsToClear = $em->getRepository(UserNotification::class)->findBy(array('user' => $this->getUser()));

        foreach($notificationsToClear as $notification) {
            $em->remove($notification);
        }
        $em->flush();

        // Return to the previous URL
        $returnUrl = $request->query->get('returnUrl');
        return $this->redirect($returnUrl);
    }
}
