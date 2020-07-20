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
use App\Entity\UserNotification;
use App\Entity\Promo;

class ModactionsController extends AbstractController
{
    /**
     * @Route("/moderation/song/{songId}/remove", name="moderation.song.remove")
     */
    public function songRemove(Request $request, int $songId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $songToRemove = $em->getRepository(Song::class)->findOneBy(array('id' => $songId));
        $uploader = $em->getRepository(Song::class)->findOneBy(array('id' => $songToRemove->getUploader()));

        try {
            $message = (new \Swift_Message('Your song '.$songToRemove->getTitle().' was removed!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($uploader->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/songRemoved.txt.twig',
                                ['song' => $songToRemove]
                            ), 'text/plain');

            $mailer->send($message);
        } catch ( \Exception $e ) { }

        // Remove .srtb File
        try {
            @unlink($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$songToRemove->getFileReference().".srtb");
        } catch(FileNotFoundException $e) {

        }

        // Remove cover
        try {
            @unlink($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$songToRemove->getFileReference().".png");
        } catch(FileNotFoundException $e) {

        }

        // Remove .ogg files
        try {
            $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$songToRemove->getFileReference()."_*.ogg");
            foreach($oggFiles as $oggFile) {
                @unlink($oggFile);
            }
        } catch(FileNotFoundException $e) {

        }
        
        $em->remove($songToRemove);

        $notifications = $em->getRepository(UserNotification::class)->findBy(array('connectedSong' => $songToRemove));
        foreach($notifications as $notification) {
            $em->remove($notification);
        }

        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $uploader->getId()));
    }
    
    /**
     * @Route("/moderation/reports/song/{reportId}/remove", name="moderation.reports.song.remove")
     */
    public function reportsSongRemove(Request $request, int $reportId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $report = $em->getRepository(SongReport::class)->findOneBy(array('id' => $reportId));
        $reportSong = $em->getRepository(Song::class)->findOneBy(array('id' => $report->getSongId()));
        $reportSongUploader = $em->getRepository(User::class)->findOneBy(array('id' => $reportSong->getUploader()));

        // Remove .srtb File
        try {
            @unlink($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$reportSong->getFileReference().".srtb");
        } catch(FileNotFoundException $e) {

        }

        // Remove cover
        try {
            @unlink($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$reportSong->getFileReference().".png");
        } catch(FileNotFoundException $e) {

        }

        // Remove .ogg files
        try {
            $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$reportSong->getFileReference()."_*.ogg");
            foreach($oggFiles as $oggFile) {
                @unlink($oggFile);
            }
        } catch(FileNotFoundException $e) {

        }

        try {
            $message = (new \Swift_Message('Your song '.$reportSong->getTitle().' was removed!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($reportSongUploader->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/songRemovedReportUser.txt.twig',
                                ['song' => $reportSong, 'report' => $report]
                            ), 'text/plain');

            $mailer->send($message);

            $message = (new \Swift_Message('[#SONG-'.$report->getId().'] Action was taken for your report!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($report->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/songRemovedReportReporter.txt.twig',
                                ['report' => $report, 'song' => $reportSong]
                            ), 'text/plain');

            $mailer->send($message);
        } catch ( \Exception $e ) { }
        
        $em->remove($reportSong);

        $notifications = $em->getRepository(UserNotification::class)->findBy(array('connectedSong' => $reportSong));
        foreach($notifications as $notification) {
            $em->remove($notification);
        }

        $em->flush();

        return $this->redirectToRoute('moderation.reports.song', array('reportId' => $reportId));
    }
    
    /**
     * @Route("/moderation/user/ban/{userId}", name="moderation.user.ban")
     */
    public function userBan(Request $request, int $userId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $userToBan = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        $userToBan->setEnabled(false);

        try {
            $message = (new \Swift_Message('Your account was banned!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($userToBan->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/userBanned.txt.twig'
                            ), 'text/plain');

            $mailer->send($message);
        } catch ( \Exception $e ) { }

        $em->persist($userToBan);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
    
    /**
     * @Route("/moderation/user/unban/{userId}", name="moderation.user.unban")
     */
    public function userUnban(Request $request, int $userId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $userToUnban = $em->getRepository(user::class)->findOneBy(array('id' => $userId));
        $userToUnban->setEnabled(true);

        try {
            $message = (new \Swift_Message('Your account was unbanned!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($userToUnban->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/userUnbanned.txt.twig'
                            ), 'text/plain');

            $mailer->send($message);
        } catch ( \Exception $e ) { }

        $em->persist($userToUnban);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
    
    /**
     * @Route("/moderation/user/toggleVerified/{userId}", name="moderation.user.toggleVerified")
     */
    public function userToggleVerified(Request $request, int $userId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $userToToggle = $em->getRepository(user::class)->findOneBy(array('id' => $userId));
        $userToToggle->setIsVerified(!$userToToggle->getIsVerified());

        try {
            $message = (new \Swift_Message('Your verification status changed!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($userToToggle->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/userVerification.txt.twig',
                                ['user' => $userToToggle]
                            ), 'text/plain');

            $mailer->send($message);
        } catch ( \Exception $e ) { }

        $em->persist($userToToggle);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
    
    /**
     * @Route("/moderation/user/togglePatreon/{userId}", name="moderation.user.togglePatreon")
     */
    public function userTogglePatreon(Request $request, int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $userToToggle = $em->getRepository(user::class)->findOneBy(array('id' => $userId));
        $userToToggle->setIsPatreon(!$userToToggle->getIsPatreon());

        $em->persist($userToToggle);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
    
    /**
     * @Route("/moderation/user/toggleMod/{userId}", name="moderation.user.toggleMod")
     */
    public function userToggleMod(Request $request, int $userId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $userToToggle = $em->getRepository(user::class)->findOneBy(array('id' => $userId));

        if($userToToggle->hasRole("ROLE_MODERATOR")) {
            $userToToggle->removeRole("ROLE_MODERATOR");
        } else {
            $userToToggle->addRole("ROLE_MODERATOR");
        }

        $em->persist($userToToggle);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
    
    /**
     * @Route("/moderation/user/resetAvatar/{userId}", name="moderation.user.resetAvatar")
     */
    public function userResetAvatar(Request $request, int $userId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $userToReset = $em->getRepository(user::class)->findOneBy(array('id' => $userId));
        
        // Remove Avatar
        try {
            $fileToRemove = glob($this->getParameter('avatar_path').DIRECTORY_SEPARATOR.$userToReset->getCoverReference());
            if(count($fileToRemove) > 0) {
                @unlink($fileToRemove[0]);
            }
            $userToReset->setCoverReference("");
        } catch(FileException $e) {

        }

        try {
            $message = (new \Swift_Message('Your user avatar was reset!'))
                        ->setFrom('legal@spinsha.re')
                            ->setTo($userToReset->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/userAvatarReset.txt.twig'
                            ), 'text/plain');

            $mailer->send($message);
        } catch ( \Exception $e ) { }

        // Remove Entity
        $em->persist($userToReset);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
}
