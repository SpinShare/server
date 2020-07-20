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
use Symfony\Component\HttpFoundation\Session\Session;

use App\Utils\HelperFunctions;
use App\Entity\User;
use App\Entity\UserNotification;
use App\Entity\Song;

class SystemController extends AbstractController
{
    /**
     * @Route("/moderation/system/", name="moderation.system.index")
     */
    public function systemIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        return $this->render('moderation/system/index.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/system/notificationToEveryone", name="moderation.system.notificationToEveryone")
     */
    public function systemNotificationToEveryone(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $allUsers = $em->getRepository(User::class)->findAll();

        foreach($allUsers as $user) {
            $newNotification = new UserNotification();
            $newNotification->setUser($user);
            $newNotification->setNotificationType(0);
            $newNotification->setNotificationData($request->request->get('notificationData'));

            $em->persist($newNotification);
        }

        $em->flush();

        return $this->redirectToRoute('moderation.system.index');
    }
    
    /**
     * @Route("/moderation/system/cleanup/temp", name="moderation.system.cleanup.temp")
     */
    public function systemCleanupTemp(Request $request)
    {
        $filesToRemove = glob($this->getParameter('temp_path').DIRECTORY_SEPARATOR."*");
        foreach($filesToRemove as $fileToRemove) {
            try {
                @unlink($fileToRemove);
            } catch(FileNotFoundException $e) {

            }
        }

        return $this->redirectToRoute('moderation.system.index');
    }
    
    /**
     * @Route("/moderation/system/generate/thumbnails", name="moderation.system.generate.thumbnails")
     */
    public function systemGenerateThumbnails(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        
        $allSongs = $em->getRepository(Song::class)->findAll();

        try {
            foreach($allSongs as $oneSong) {
                $filePath = glob($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".png");
                if(count($filePath) > 0) {
                    $hf = new HelperFunctions();
                    $hf->generateThumbnail($filePath[0], $this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".jpg", 300);
                }
            }
        } catch(\Exception $e) {
            var_dump($e);
            exit;
        }

        return $this->redirectToRoute('moderation.system.index');
    }
    
    /**
     * @Route("/moderation/system/generate/thumbnails-missing", name="moderation.system.generate.thumbnails-missing")
     */
    public function systemGenerateMissingThumbnails(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        
        $allSongs = $em->getRepository(Song::class)->findAll();

        try {
            foreach($allSongs as $oneSong) {
                $filePath = glob($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".png");
                $existingThumbnail = glob($this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".jpg");

                if(count($existingThumbnail) == 0) {
                    if(count($filePath) > 0) {
                        $hf = new HelperFunctions();
                        $hf->generateThumbnail($filePath[0], $this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".jpg", 300);
                        echo "Generating: ".$oneSong->getFileReference()."<br />";
                    }
                } else {
                    echo "Skipping: ".$oneSong->getFileReference()."<br />";
                }
            }
        } catch(\Exception $e) {
            var_dump($e);
            exit;
        }

        exit;

        return $this->redirectToRoute('moderation.system.index');
    }
}
