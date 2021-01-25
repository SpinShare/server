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

        return $this->redirectToRoute('moderation.system.index');
    }
    
    /**
     * @Route("/moderation/system/migration/difficultyRating/{songID}", name="moderation.system.migration.difficultyRating")
     */
    public function systemMigrationDifficultyRating(Request $request, int $songID = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        // Calculate Max Count
        $songCount = $em->getRepository(Song::class)->createQueryBuilder('u')->select('MAX(u.id)')->getQuery()->getSingleScalarResult();
        if($songID > $songCount) {
            return $this->redirectToRoute('moderation.system.index');
        }
        
        // Find Song
        $songToProcess = $em->getRepository(Song::class)->findOneBy(array('id' => $songID));

        // Go To Next if not found
        if($songToProcess == null) {
            return $this->render('moderation/migration/difficultyRating.html.twig', array('songID' => $songID + 1));
        }

        // Calculate MD5 Hash
        try {
            // Load SRTB file
            $srtbPath = glob($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$songToProcess->getFileReference().".srtb");
            $srtbContent = json_decode(file_get_contents($srtbPath[0]));

            $trackInfo = null;
            $clipInfo = [];
            $trackData = [];

            foreach($srtbContent->largeStringValuesContainer->values as $valueItem) {
                if(strpos($valueItem->key, "TrackInfo") !== false) {
                    $trackInfo = json_decode($valueItem->val);
                }
                if(strpos($valueItem->key, "ClipInfo") !== false) {
                    $i = str_replace("SO_ClipInfo_ClipInfo_", "", $valueItem->key);
                    $clipInfo[$i] = json_decode($valueItem->val);
                }
                if(strpos($valueItem->key, "TrackData") !== false) {
                    $i = str_replace("SO_TrackData_TrackData_", "", $valueItem->key);
                    $trackData[$i] = json_decode($valueItem->val);
                }
            }

            // Reset Values
            $songToProcess->setHasEasyDifficulty(false);
            $songToProcess->setHasNormalDifficulty(false);
            $songToProcess->setHasHardDifficulty(false);
            $songToProcess->setHasExtremeDifficulty(false);
            $songToProcess->setHasXDDifficulty(false);
            
            // Detect used difficulties
            foreach($trackInfo->difficulties as $oneData) {
                if(isset($oneData->_active) && $oneData->_active || !isset($oneData->_active)) {
                    switch($oneData->_difficulty) {
                        case 2:
                            $songToProcess->setHasEasyDifficulty(true);
                            break;
                        case 3:
                            $songToProcess->setHasNormalDifficulty(true);
                            break;
                        case 4:
                            $songToProcess->setHasHardDifficulty(true);
                            break;
                        case 5:
                            $songToProcess->setHasExtremeDifficulty(true);
                            break;
                        case 6:
                            $songToProcess->setHasXDDifficulty(true);
                            break;
                    }
                }
            }

            // Reset difficulty ratings
            $songToProcess->setEasyDifficulty(false);
            $songToProcess->setNormalDifficulty(false);
            $songToProcess->setHardDifficulty(false);
            $songToProcess->setExpertDifficulty(false);
            $songToProcess->setXDDifficulty(false);

            // Detect difficulty ratings
            foreach($trackData as $trackDataItem) {
                switch($trackDataItem->difficultyType) {
                    case 2:
                        $songToProcess->setEasyDifficulty($trackDataItem->difficultyRating);
                        break;
                    case 3:
                        $songToProcess->setNormalDifficulty($trackDataItem->difficultyRating);
                        break;
                    case 4:
                        $songToProcess->setHardDifficulty($trackDataItem->difficultyRating);
                        break;
                    case 5:
                        $songToProcess->setExpertDifficulty($trackDataItem->difficultyRating);
                        break;
                    case 6:
                        $songToProcess->setXDDifficulty($trackDataItem->difficultyRating);
                        break;
                }
            }

            $em->persist($songToProcess);
            $em->flush();

            // Go To Next
            return $this->render('moderation/migration/difficultyRating.html.twig', array('songID' => $songID + 1));
        } catch(\Exception $e) {
            var_dump($e);
            exit;
        }
    }
    
    /**
     * @Route("/moderation/system/migration/updateHash/{songID}", name="moderation.system.migration.updateHash")
     */
    public function systemMigrationUpdateHash(Request $request, int $songID = 1)
    {
        $em = $this->getDoctrine()->getManager();
        $baseUrl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();

        // Calculate Max Count
        $songCount = $em->getRepository(Song::class)->createQueryBuilder('u')->select('MAX(u.id)')->getQuery()->getSingleScalarResult();
        if($songID > $songCount) {
            return $this->redirectToRoute('moderation.system.index');
        }
        
        // Find Song
        $songToProcess = $em->getRepository(Song::class)->findOneBy(array('id' => $songID));

        // Go To Next if not found
        if($songToProcess == null) {
            return $this->render('moderation/migration/updateHash.html.twig', array('songID' => $songID + 1));
        }

        // Calculate MD5 Hash
        try {
            // Load SRTB file
            $srtbPath = glob($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$songToProcess->getFileReference().".srtb");
            $srtbContentRaw = file_get_contents($srtbPath[0]);

            // Save MD5 Hash
            $songToProcess->setUpdateHash(md5($srtbContentRaw));

            $em->persist($songToProcess);
            $em->flush();

            // Go To Next
            return $this->render('moderation/migration/updateHash.html.twig', array('songID' => $songID + 1));
        } catch(\Exception $e) {
            var_dump($e);
            exit;
        }
    }
}
