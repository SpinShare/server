<?php

namespace App\Controller;

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
use App\Entity\Promo;

class ModerationController extends AbstractController
{
    /**
     * @Route("/moderation", name="moderation.index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $openUserReports = $em->getRepository(UserReport::class)->findBy(array(), array('status' => 'ASC', 'reportDate' => 'DESC'));
        $openSongReports = $em->getRepository(SongReport::class)->findBy(array(), array('status' => 'ASC', 'reportDate' => 'DESC'));

        $promos = $em->getRepository(Promo::class)->findBy(array(), array('id' => 'DESC'));
        $clientReleases = $em->getRepository(ClientRelease::class)->findBy(array(), array('uploadDate' => 'DESC'));

        $data['userReports'] = $openUserReports;
        $data['songReports'] = $openSongReports;
        $data['promos'] = $promos;
        $data['clientReleases'] = $clientReleases;

        return $this->render('moderation/index.html.twig', $data);
    }

    /**
     * @Route("/moderation/clientReleases/add", name="moderation.clientreleases.add")
     */
    public function clientReleasesAdd(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $form = $this->createFormBuilder()
                        ->add('executableFile', FileType::class, ['label' => 'Executable File', 'attr' => array('accept' => '.exe, .dmg')])
                        ->add('majorVersion', TextType::class, ['label' => 'Major Version'])
                        ->add('minorVersion', TextType::class, ['label' => 'Minor Version'])
                        ->add('patchVersion', TextType::class, ['label' => 'Patch Version'])
                        ->add('platform', ChoiceType::class, ['label' => 'Platform', 'choices' => array('Windows' => 'win32', 'Mac' => 'mac')])
                        ->add('save', SubmitType::class, ['label' => 'Release'])
                        ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $newRelease = new ClientRelease();
                $newRelease->setMajorVersion($data['majorVersion']);
                $newRelease->setMinorVersion($data['minorVersion']);
                $newRelease->setPatchVersion($data['patchVersion']);
                $newRelease->setPlatform($data['platform']);
                $newRelease->setUploadDate(new \DateTime());

                $newFilename = "SpinShare_".$data['platform']."_".$data['majorVersion']."_".$data['minorVersion']."_".$data['patchVersion'].".exe";
                $data['executableFile']->move($this->getParameter('client_path'), $newFilename);

                $newRelease->setFileReference($newFilename);

                $em->persist($newRelease);
                $em->flush();

                return $this->redirectToRoute('moderation.index');
            } catch(FileException $e) {

            }
        }

        $data['addForm'] = $form->createView();

        return $this->render('moderation/clientReleases/add.html.twig', $data);
    }

    /**
     * @Route("/moderation/clientReleases/remove/{releaseId}", name="moderation.clientreleases.remove")
     */
    public function clientReleasesRemove(Request $request, int $releaseId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $releaseToRemove = $em->getRepository(ClientRelease::class)->findOneBy(array('id' => $releaseId));
        
        // Remove EXE file
        try {
            $fileToRemove = glob($this->getParameter('client_path').DIRECTORY_SEPARATOR.$releaseToRemove->getFileReference());
            if(count($fileToRemove) > 0) {
                @unlink($fileToRemove[0]);
            }
        } catch(FileException $e) {

        }

        // Remove Entity
        $em->remove($releaseToRemove);
        $em->flush();

        return $this->redirectToRoute('moderation.index');
    }

    /**
     * @Route("/moderation/promos/add", name="moderation.promos.add")
     */
    public function promosAdd(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $form = $this->createFormBuilder()
                        ->add('imagePath', FileType::class, ['label' => 'Banner File (500x256px)', 'attr' => array('accept' => '.png, .jpg, .jpeg')])
                        ->add('title', TextType::class, ['label' => 'Title'])
                        ->add('type', TextType::class, ['label' => 'Type'])
                        ->add('textColor', TextType::class, ['label' => 'Text Color'])
                        ->add('color', TextType::class, ['label' => 'Primary Color'])
                        ->add('buttonType', ChoiceType::class, ['label' => 'Button Type', 'choices' => array('Song' => 0, 'Playlist (Unused)' => 1, 'Search Query' => 2, 'External' => 3)])
                        ->add('buttonData', TextType::class, ['label' => 'Button Data'])
                        ->add('isVisible', ChoiceType::class, ['label' => 'Is Visible?', 'choices' => array('Yes' => true, 'No' => false)])
                        ->add('save', SubmitType::class, ['label' => 'Save'])
                        ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $newPromo = new Promo();

                $newPromo->setTitle($data['title']);
                $newPromo->setType($data['type']);
                $newPromo->setTextColor($data['textColor']);
                $newPromo->setColor($data['color']);
                $newPromo->setButtonType($data['buttonType']);
                $newPromo->setButtonData($data['buttonData']);
                $newPromo->setIsVisible($data['isVisible']);

                $newFilename = "promo_".uniqid().".png";
                $data['imagePath']->move($this->getParameter('promo_path'), $newFilename);

                $newPromo->setImagePath($newFilename);

                $em->persist($newPromo);
                $em->flush();

                return $this->redirectToRoute('moderation.index');
            } catch(FileException $e) {

            }
        }

        $data['addForm'] = $form->createView();

        return $this->render('moderation/promos/add.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/promos/switchVisibility/{promoId}", name="moderation.promos.switchVisibility")
     */
    public function promosSwitchVisibility(Request $request, int $promoId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $promoToSwitch = $em->getRepository(Promo::class)->findOneBy(array('id' => $promoId));
        
        $promoToSwitch->setIsVisible(!$promoToSwitch->getIsVisible());

        $em->persist($promoToSwitch);
        $em->flush();

        return $this->redirectToRoute('moderation.index');
    }
    
    /**
     * @Route("/moderation/promos/remove/{promoId}", name="moderation.promos.remove")
     */
    public function promosRemove(Request $request, int $promoId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $promoToRemove = $em->getRepository(Promo::class)->findOneBy(array('id' => $promoId));
        
        // Remove PNG file
        try {
            $fileToRemove = glob($this->getParameter('promo_path').DIRECTORY_SEPARATOR.$promoToRemove->getImagePath());
            if(count($fileToRemove) > 0) {
                @unlink($fileToRemove[0]);
            }
        } catch(FileException $e) {

        }

        // Remove Entity
        $em->remove($promoToRemove);
        $em->flush();

        return $this->redirectToRoute('moderation.index');
    }

    /**
     * @Route("/moderation/reports/user/{reportId}", name="moderation.reports.user")
     */
    public function reportsUserDetail(Request $request, int $reportId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $report = $em->getRepository(UserReport::class)->findOneBy(array('id' => $reportId));
        $reportUser = $em->getRepository(User::class)->findOneBy(array('id' => $report->getUserId()));

        $data['report'] = $report;
        $data['reportUser'] = $reportUser;

        return $this->render('moderation/reports/user.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/reports/user/{reportId}/status/{newStatus}", name="moderation.reports.user.changeStatus")
     */
    public function reportsUserChangeStatus(Request $request, int $reportId, int $newStatus, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $reportToChange = $em->getRepository(UserReport::class)->findOneBy(array('id' => $reportId));

        $message = (new \Swift_Message('[#USER-'.$reportToChange->getId().'] Your reports status changed!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($reportToChange->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/changeStatus.txt.twig',
                            ['report' => $reportToChange, 'type' => 'USER']
                        ), 'text/plain');

        @$mailer->send($message);

        $reportToChange->setStatus($newStatus);

        $em->persist($reportToChange);
        $em->flush();

        return $this->redirectToRoute('moderation.reports.user', array('reportId' => $reportId));
    }

    /**
     * @Route("/moderation/reports/song/{reportId}", name="moderation.reports.song")
     */
    public function reportsSongDetail(Request $request, int $reportId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $report = $em->getRepository(SongReport::class)->findOneBy(array('id' => $reportId));
        $data['report'] = $report;

        $reportSong = $em->getRepository(Song::class)->findOneBy(array('id' => $report->getSongId()));
        $data['reportSong'] = $reportSong;

        if($reportSong) {
            $reportSongUploader = $em->getRepository(User::class)->findOneBy(array('id' => $reportSong->getUploader()));
            $data['reportSongUploader'] = $reportSongUploader;
        }

        return $this->render('moderation/reports/song.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/reports/song/{reportId}/status/{newStatus}", name="moderation.reports.song.changeStatus")
     */
    public function reportsSongChangeStatus(Request $request, int $reportId, int $newStatus, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $reportToChange = $em->getRepository(SongReport::class)->findOneBy(array('id' => $reportId));

        $message = (new \Swift_Message('[#SONG-'.$reportToChange->getId().'] Your reports status changed!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($reportToChange->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/changeStatus.txt.twig',
                            ['report' => $reportToChange, 'type' => 'SONG']
                        ), 'text/plain');

        @$mailer->send($message);

        $reportToChange->setStatus($newStatus);

        $em->persist($reportToChange);
        $em->flush();

        return $this->redirectToRoute('moderation.reports.song', array('reportId' => $reportId));
    }
    
    /**
     * @Route("/moderation/song/{songId}/remove", name="moderation.song.remove")
     */
    public function songRemove(Request $request, int $songId, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $songToRemove = $em->getRepository(Song::class)->findOneBy(array('id' => $songId));
        $uploader = $em->getRepository(Song::class)->findOneBy(array('id' => $songToRemove->getUploader()));

        $message = (new \Swift_Message('Your song '.$songToRemove->getTitle().' was removed!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($uploader->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/songRemoved.txt.twig',
                            ['song' => $songToRemove]
                        ), 'text/plain');

        $mailer->send($message);

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
        
        $em->remove($reportSong);
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

        $message = (new \Swift_Message('Your account was banned!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($userToBan->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/userBanned.txt.twig'
                        ), 'text/plain');

        $mailer->send($message);

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

        $message = (new \Swift_Message('Your account was unbanned!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($userToUnban->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/userUnbanned.txt.twig'
                        ), 'text/plain');

        $mailer->send($message);

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

        $message = (new \Swift_Message('Your verification status changed!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($userToToggle->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/userVerification.txt.twig',
                            ['user' => $userToToggle]
                        ), 'text/plain');

        $mailer->send($message);

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

        $message = (new \Swift_Message('Your user avatar was reset!'))
                    ->setFrom('legal@spinsha.re')
                    ->setTo($userToReset->getEmail())
                    ->setBody(
                        $this->renderView(
                            'emails/moderation/userAvatarReset.txt.twig'
                        ), 'text/plain');

        $mailer->send($message);

        // Remove Entity
        $em->persist($userToReset);
        $em->flush();

        return $this->redirectToRoute('user.detail', array('userId' => $userId));
    }
    
    /**
     * @Route("/moderation/system/deploy", name="moderation.system.deploy")
     */
    public function systemDeploy(Request $request)
    {
        shell_exec("bash /var/www/spinshare/pull-volatile.sh");

        return $this->redirectToRoute('moderation.index');
    }
    
    /**
     * @Route("/moderation/system/rollback", name="moderation.system.rollback")
     */
    public function systemRollback(Request $request)
    {
        shell_exec("dep rollback");

        return $this->redirectToRoute('moderation.index');
    }
}
