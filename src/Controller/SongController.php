<?php

namespace App\Controller;

use App\Entity\SongPlaylist;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Song;
use App\Entity\SongReview;
use App\Entity\SongSpinPlay;
use App\Utils\HelperFunctions;
use App\Entity\User;
use App\Entity\UserNotification;

class SongController extends AbstractController
{
    /**
     * @Route("/song/{songId}", name="song.detail")
     */
    public function songDetail(Request $request, $songId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if(strpos($songId, "spinshare_") !== false) {
            $resultSong = $em->getRepository(Song::class)->findOneBy(array('fileReference' => $songId));
            $songId = $resultSong->getId();
        } else {
            $resultSong = $em->getRepository(Song::class)->findOneBy(array('id' => $songId));
        }

        if(!$resultSong) throw new NotFoundHttpException();

        $resultSong->setViews($resultSong->getViews() + 1);
        $em->persist($resultSong);
        $em->flush();

        $resultUploader = $em->getRepository(User::class)->findOneBy(array('id' => $resultSong->getUploader()));
        if(!$resultUploader) throw new NotFoundHttpException();

        $resultReviews = $em->getRepository(SongReview::class)->findBy(array('song' => $resultSong), array('reviewDate' => 'DESC'));
        $resultSpinPlay = $em->getRepository(SongSpinPlay::class)->findBy(array('song' => $resultSong, 'isActive' => true), array('submitDate' => 'DESC'));

        $resultReviewAverage = $em->getRepository(SongReview::class)->getAverageByID($songId);

        $data['song'] = $resultSong;
        $data['uploader'] = $resultUploader;
        $data['reviews'] = $resultReviews;
        $data['spinplays'] = $resultSpinPlay;
        $data['reviewAverage'] = $resultReviewAverage;
        $data['activeTab'] = $request->query->get('tab') ? $request->query->get('tab') : 'reviews';
        $data['activeAction'] = $request->query->get('action');

        if($this->getUser() != null) {
            $resultUserPlaylists = $em->getRepository(SongPlaylist::class)->findBy(array('user' => $this->getUser()));

            if($request->request->get('submitPlaylist')) {
                $allValues = $request->request->all();

                foreach($resultUserPlaylists as $playlist) {
                    $playlist->removeSong($resultSong);
                    $em->persist($playlist);
                }

                foreach($allValues as $key => $value) {
                    if(strpos($key, "playlist_") !== false) {
                        if($value == "on") {
                            $checkedPlaylist = $resultUserPlaylists[str_replace("playlist_", "", $key)];
                            $checkedPlaylist->addSong($resultSong);
                            $em->persist($checkedPlaylist);
                        }
                    }
                }

                $em->flush();
            }

            $data['userPlaylists'] = $resultUserPlaylists;

            $resultUserReview = $em->getRepository(SongReview::class)->findBy(array('song' => $resultSong, 'user' => $this->getUser()));
            $data['userReview'] = $resultUserReview;

            if($this->getUser() != $resultUploader) {
                if($request->request->get('submitReview') && !empty($request->request->get('reviewRecommended'))) {
                    $newReview = new SongReview();
                    $newReview->setUser($this->getUser());
                    $newReview->setSong($resultSong);
                    $newReview->setRecommended($request->request->get('reviewRecommended') == "yes" ? true : false);
                    $newReview->setComment($request->request->get('reviewComment'));
                    $newReview->setReviewDate(new \DateTime('NOW'));

                    $em->persist($newReview);
                    $em->flush();
   
                    $newNotification = new UserNotification();
                    $newNotification->setUser($resultUploader);
                    $newNotification->setConnectedUser($this->getUser());
                    $newNotification->setConnectedSong($resultSong);
                    $newNotification->setNotificationType(1);
                    $newNotification->setNotificationData($newReview->getID());
                    $em->persist($newNotification);
                    $em->flush();

                    return $this->redirectToRoute('song.detail', ['songId' => $resultSong->getId(), 'tab' => 'reviews']);
                }
            }

            if($request->request->get('submitSpinPlay') && !empty($request->request->get('spinPlayUrl'))) {
                $newSpinPlay = new SongSpinPlay();
                $newSpinPlay->setUser($this->getUser());
                $newSpinPlay->setVideoUrl($request->request->get('spinPlayUrl'));
                $newSpinPlay->setSong($resultSong);
                $newSpinPlay->setSubmitDate(new \DateTime('NOW'));
                $newSpinPlay->setIsActive(true);

                $em->persist($newSpinPlay);
                $em->flush();
   
                $newNotification = new UserNotification();
                $newNotification->setUser($resultUploader);
                $newNotification->setConnectedUser($this->getUser());
                $newNotification->setConnectedSong($resultSong);
                $newNotification->setNotificationType(2);
                $newNotification->setNotificationData($newSpinPlay->getID());
                $em->persist($newNotification);
                $em->flush();

                return $this->redirectToRoute('song.detail', ['songId' => $resultSong->getId(), 'tab' => 'spinplays']);
            }
        } else {
            $data['userReview'] = false;
        }

        return $this->render('song/detail.html.twig', $data);
    }
    /**
     * @Route("/song/{songId}/download", name="song.download")
     */
    public function songDownload(Request $request, int $songId)
    {
        $em = $this->getDoctrine()->getManager();

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $songId));

        if(!$result) {
            throw new NotFoundHttpException();
        } else {
            $result->setDownloads($result->getDownloads() + 1);
            $em->persist($result);
            $em->flush();

            $zipLocation = $this->getParameter('temp_path').DIRECTORY_SEPARATOR;
            $zipName = $result->getFileReference().".zip";

            try {
                $coverFiles = glob($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$result->getFileReference().".png");
                $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$result->getFileReference()."_*.ogg");

                $zip = new \ZipArchive;
                $zip->open($zipLocation.$zipName, \ZipArchive::CREATE);
                $zip->addFile($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$result->getFileReference().".srtb", $result->getFileReference().".srtb");
                if(is_file($coverFiles[0])) {
                    $zip->addFile($coverFiles[0], "AlbumArt".DIRECTORY_SEPARATOR.$result->getFileReference().".png");
                }
                if(count($oggFiles) > 0) {
                    foreach($oggFiles as $oggFile) {
                        $oggIndex = explode(".", explode("_", $oggFile)[2])[0];
                        $zip->addFile($oggFile, "AudioClips".DIRECTORY_SEPARATOR.$result->getFileReference()."_".$oggIndex.".ogg");
                    }
                }
                $zip->close();

                $response = new Response(file_get_contents($zipLocation.$zipName));
                $response->headers->set('Content-Type', 'application/zip');
                $response->headers->set('Content-Disposition', 'attachment;filename="' . $zipName . '"');
                $response->headers->set('Content-length', filesize($zipLocation.$zipName));
            } catch(Exception $e) {
                throw new NotFoundHttpException();
            }
        
            @unlink($zipLocation.$zipName);
        
            return $response;
        }
    }

    /**
     * @Route("/song/{songId}/update", name="song.update")
     */
    public function songUpdate(Request $request, int $songId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $song = $em->getRepository(Song::class)->findOneBy(array('id' => $songId, 'uploader' => $user->getId()));
        
        if(!$song) {
            throw new NotFoundHttpException();
        } else {
            $form = $this->createFormBuilder()
            ->add('backupPath', FileType::class, ['label' => 'Backup .zip', 'required' => false, 'row_attr' => array('class' => 'upload-field'), 'attr' => array('accept' => '.zip')])
            ->add('tags', TextType::class, ['label' => 'Tags', 'row_attr' => array('class' => 'tags-field'), 'required' => false, 'data' => $song->getTags()])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => array('rows' => 5), 'row_attr' => array('class' => 'tags-field'), 'required' => false, 'data' => $song->getDescription()])
            ->add('isExplicit', CheckboxType::class, ['label' => 'Is the song explicit?', 'row_attr' => array('class' => "tags-field"), 'required' => false, 'data' => $song->getIsExplicit()])
            ->add('publicationStatus', ChoiceType::class, ['label' => 'Publication Status', 'row_attr' => array('class' => "tags-field"), 'required' => true, 'choices' => ['Public' => 0, 'Hide from lists' => 1, 'Unlisted' => 2]])
            ->add('save', SubmitType::class, ['label' => 'Upload'])
            ->getForm();

            $form->handleRequest($request);

            $tempVars['uploadForm'] = $form->createView();

            if($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();

                $backupFile = $data['backupPath'];

                $song->setTags($data['tags']);
                $song->setDescription($data['description']);
                $song->setIsExplicit($data['isExplicit']);
                $song->setPublicationStatus($data['publicationStatus']);

                if($backupFile) {
                    $zip = new \ZipArchive;
                    if($zip->open($backupFile)) {
                        try {
                            // extract backup
                            $extractionPath = $this->getParameter('temp_path').DIRECTORY_SEPARATOR.$song->getFileReference();
                            $zip->extractTo($extractionPath);
                            $zip->close();

                            // clean up old song data
                            // Remove .srtb File
                            try {
                                @unlink($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$song->getFileReference().".srtb");
                            } catch(FileNotFoundException $e) {
        
                            }
        
                            // Remove cover
                            try {
                                @unlink($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$song->getFileReference().".png");
                            } catch(FileNotFoundException $e) {
        
                            }
        
                            // Remove thumbnail
                            try {
                                @unlink($this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$song->getFileReference().".jpg");
                            } catch(FileNotFoundException $e) {
        
                            }
        
                            // Remove .ogg files
                            try {
                                $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$song->getFileReference()."_*.ogg");
                                foreach($oggFiles as $oggFile) {
                                    @unlink($oggFile);
                                }
                            } catch(FileNotFoundException $e) {
        
                            }

                            try {
                                try {
                                    // get backup data
                                    $srtbFiles = glob($extractionPath.DIRECTORY_SEPARATOR."*.srtb");
                                    $srtbContent = json_decode(file_get_contents($srtbFiles[0]));

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

                                    // set meta data
                                    $song->setUploader($user->getId());
                                    $song->setTitle($trackInfo->title);
                                    $song->setSubtitle($trackInfo->subtitle);
                                    $song->setArtist($trackInfo->artistName);
                                    $song->setCharter($trackInfo->charter);

                                    $song->setHasEasyDifficulty(false);
                                    $song->setHasNormalDifficulty(false);
                                    $song->setHasHardDifficulty(false);
                                    $song->setHasExtremeDifficulty(false);
                                    $song->setHasXDDifficulty(false);

                                    foreach($trackInfo->difficulties as $oneData) {
                                        if(isset($oneData->_active) && $oneData->_active || !isset($oneData->_active)) {
                                            switch($oneData->_difficulty) {
                                                case 2:
                                                    $song->setHasEasyDifficulty(true);
                                                    break;
                                                case 3:
                                                    $song->setHasNormalDifficulty(true);
                                                    break;
                                                case 4:
                                                    $song->setHasHardDifficulty(true);
                                                    break;
                                                case 5:
                                                    $song->setHasExtremeDifficulty(true);
                                                    break;
                                                case 6:
                                                    $song->setHasXDDifficulty(true);
                                                    break;
                                            }
                                        }
                                    }
                                } catch(Exception $e) {
                                    var_dump($e);

                                    // clean up temp files
                                    $hf = new HelperFunctions();
                                    $hf->delTree($extractionPath);
                                }

                                try {
                                    // find cover
                                    $coverFiles = glob($extractionPath.DIRECTORY_SEPARATOR."AlbumArt".DIRECTORY_SEPARATOR.$trackInfo->albumArtReference->assetName.".*");
                                    if($coverFiles[0]) {
                                        $fileType = explode(".", $coverFiles[0])[count(explode(".", $coverFiles[0])) - 1];
                                        
                                        if(in_array($fileType, array('jpg', 'png'))) {
                                            // Generate Thumbnail
                                            $hf = new HelperFunctions();
                                            $hf->generateThumbnail($coverFiles[0], $this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$song->getFileReference().".jpg", 300);

                                            $trackInfo->albumArtReference->assetName = $song->getFileReference();
                                            rename($coverFiles[0], $this->getParameter('cover_path').DIRECTORY_SEPARATOR.$song->getFileReference().".png");
                                        }
                                    }
                                } catch(Exception $e) {
                                    var_dump($e);

                                    // clean up temp files
                                    $hf = new HelperFunctions();
                                    $hf->delTree($extractionPath);
                                }

                                try {
                                    foreach($clipInfo as $clipIndex => $clipItem) {
                                        // find audio
                                        $assetName = $clipItem->clipAssetReference->assetName;
                                        $newAssetName = $song->getFileReference()."_".$clipIndex;
                                        $oggLocation = $extractionPath.DIRECTORY_SEPARATOR."AudioClips".DIRECTORY_SEPARATOR.$assetName.".ogg";
                                        if(is_file($oggLocation)) {
                                            $clipInfo[$clipIndex]->clipAssetReference->assetName = $newAssetName;
                                            rename($oggLocation, $this->getParameter('audio_path').DIRECTORY_SEPARATOR.$newAssetName.".ogg");
                                        }
                                    }
                                } catch(Exception $e) {
                                    var_dump($e);

                                    // clean up temp files
                                    $hf = new HelperFunctions();
                                    $hf->delTree($extractionPath);
                                }

                                // write new track/clip info
                                foreach($srtbContent->largeStringValuesContainer->values as $valueItem) {
                                    if(strpos($valueItem->key, "TrackInfo") !== false) {
                                        $valueItem->val = json_encode($trackInfo);
                                    }
                                    if(strpos($valueItem->key, "ClipInfo") !== false) {
                                        $i = str_replace("SO_ClipInfo_ClipInfo_", "", $valueItem->key);
                                        $valueItem->val = json_encode($clipInfo[$i]);
                                    }
                                    if(strpos($valueItem->key, "TrackData") !== false) {
                                        $i = str_replace("SO_TrackData_TrackData_", "", $valueItem->key);
                                        $valueItem->val = json_encode($trackData[$i]);
                                    }
                                }

                                // write srtb file
                                $srtbFileLocation = $this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$song->getFileReference().".srtb";
                                file_put_contents($srtbFileLocation, json_encode( $srtbContent ));

                                // clean up temp files
                                $hf = new HelperFunctions();
                                $hf->delTree($extractionPath);
                            } catch(\Exception $e) {
                                throw $e;
                            }
                        } catch(\Exception $e) {
                            throw $e;
                        }
                    } else {
                        throw new \Exception("Error when extracting ZIP file");
                    }
                }

                // save in database
                $em->persist($song);
                $em->flush();
                
                return $this->redirectToRoute('song.detail', ['songId' => $song->getId()]);
            }

            return $this->render('song/update.html.twig', $tempVars);
        }
    }

    /**
     * @Route("/song/{songId}/delete", name="song.delete")
     */
    public function songDelete(Request $request, int $songId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $result = $em->getRepository(Song::class)->findOneBy(array('id' => $songId, 'uploader' => $user->getId()));
        $data['song'] = $result;
        
        if(!$result) {
            throw new NotFoundHttpException();
        } else {
            if($request->query->get('isConfirmed')) {
                // Remove .srtb File
                try {
                    @unlink($this->getParameter('srtb_path').DIRECTORY_SEPARATOR.$result->getFileReference().".srtb");
                } catch(FileNotFoundException $e) {

                }

                // Remove cover
                try {
                    @unlink($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$result->getFileReference().".png");
                } catch(FileNotFoundException $e) {

                }

                // Remove thumbnail
                try {
                    @unlink($this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$result->getFileReference().".jpg");
                } catch(FileNotFoundException $e) {

                }

                // Remove .ogg files
                try {
                    $oggFiles = glob($this->getParameter('audio_path').DIRECTORY_SEPARATOR.$result->getFileReference()."_*.ogg");
                    foreach($oggFiles as $oggFile) {
                        @unlink($oggFile);
                    }
                } catch(FileNotFoundException $e) {

                }

                // remove the entity
                $em->remove($result);

                $notifications = $em->getRepository(UserNotification::class)->findBy(array('connectedSong' => $songId));
                foreach($notifications as $notification) {
                    $em->remove($notification);
                }

                $em->flush();

                // reditect
                return $this->redirectToRoute('user.detail', ['userId' => $user->getId()]);
            } else {
                return $this->render('song/delete.html.twig', $data);
            }
        }
    }

    /**
     * @Route("/song/{songId}/spinplay/{spinplayId}/delete", name="song.spinplay.delete")
     */
    public function songSpinplayDelete(Request $request, int $songId, int $spinplayId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $resultSpinplay = $em->getRepository(SongSpinplay::class)->findOneBy(array('id' => $spinplayId));
        
        if(!$resultSpinplay) {
            return $this->redirectToRoute('song.detail', ['songId' => $songId, 'tab' => 'spinplays']);
        } else {
            // TODO
            $userRoles = $this->getUser()->getRoles();
            $allowedRoles = ["ROLE_ADMIN", "ROLE_SUPERADMIN", "ROLE_MODERATOR"];

            // Check if allowed to remove
            if(count(array_intersect($allowedRoles, $userRoles)) > 0 || $resultSpinplay->getUser() == $this->getUser() || $resultSpinPlay->getSong()->getUploader() == $this->getUser()) {
                $em->remove($resultSpinplay);
                $em->flush();
            }

            // Redirect
            return $this->redirectToRoute('song.detail', ['songId' => $songId, 'tab' => 'spinplays']);
        }
    }

    /**
     * @Route("/song/{songId}/review/{reviewId}/delete", name="song.review.delete")
     */
    public function songReviewDelete(Request $request, int $songId, int $reviewId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $resultReview = $em->getRepository(SongReview::class)->findOneBy(array('id' => $reviewId));
        
        if(!$resultReview) {
            return $this->redirectToRoute('song.detail', ['songId' => $songId, 'tab' => 'reviews']);
        } else {
            // TODO
            $userRoles = $this->getUser()->getRoles();
            $allowedRoles = ["ROLE_ADMIN", "ROLE_SUPERADMIN", "ROLE_MODERATOR"];

            // Check if allowed to remove
            if(count(array_intersect($allowedRoles, $userRoles)) > 0 || $resultReview->getUser() == $this->getUser()) {
                $em->remove($resultReview);
                $em->flush();
            }

            // Redirect
            return $this->redirectToRoute('song.detail', ['songId' => $songId, 'tab' => 'reviews']);
        }
    }
}
