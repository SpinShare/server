<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Song;
use App\Utils\HelperFunctions;

class UploadController extends AbstractController
{
    /**
     * @Route("/upload", name="upload.index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tempVars = [];

        $song = new Song();

        $form = $this->createFormBuilder()
                        ->add('backupPath', FileType::class, ['label' => 'Backup .zip', 'row_attr' => array('class' => 'upload-field'), 'attr' => array('accept' => '.zip')])
                        ->add('tags', TextType::class, ['label' => 'Tags', 'row_attr' => array('class' => 'tags-field'), 'required' => false])
                        ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => array('rows' => 5), 'row_attr' => array('class' => 'tags-field'), 'required' => false])
                        ->add('isExplicit', CheckboxType::class, ['label' => 'Is the song explicit?', 'row_attr' => array('class' => "tags-field"), 'required' => false])
                        ->add('save', SubmitType::class, ['label' => 'Upload'])
                        ->getForm();
        $form->handleRequest($request);

        $tempVars['uploadForm'] = $form->createView();

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $backupFile = $data['backupPath'];

            if($backupFile) {
                $song->setFileReference("spinshare_".uniqid());
                $song->setTags($data['tags']);
                $song->setDescription($data['description']);
                $song->setUploadDate(new \DateTime('NOW'));
                $song->setIsExplicit($data['isExplicit']);

                $zip = new \ZipArchive;
                if($zip->open($backupFile)) {
                    try {
                        // extract backup
                        $extractionPath = $this->getParameter('temp_path').DIRECTORY_SEPARATOR.$song->getFileReference();
                        $zip->extractTo($extractionPath);
                        $zip->close();

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
                                $song->setUploader($this->getUser()->getId());
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
                                $this->addFlash('error', 'Uploading failed. Please report back to our development team!');

                                // clean up temp files
                                $hf = new HelperFunctions();
                                $hf->delTree($extractionPath);
                            }

                            try {
                                // find cover
                                $coverFiles = glob($extractionPath.DIRECTORY_SEPARATOR."AlbumArt".DIRECTORY_SEPARATOR.$trackInfo->albumArtReference->assetName.".*");
                                if(count($coverFiles) > 0) {
                                    $fileType = explode(".", $coverFiles[0])[count(explode(".", $coverFiles[0])) - 1];
                                    
                                    if(in_array($fileType, array('jpg', 'png'))) {
                                        $trackInfo->albumArtReference->assetName = $song->getFileReference();
                                        rename($coverFiles[0], $this->getParameter('cover_path').DIRECTORY_SEPARATOR.$song->getFileReference().".png");

                                        // Generate Thumbnail
                                        $hf = new HelperFunctions();
                                        $hf->generateThumbnail($coverFiles[0], $this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$song->getFileReference().".jpg", 300);
                                    }
                                }
                            } catch(Exception $e) {
                                $this->addFlash('error', 'Uploading failed. Please report back to our development team!');

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
                                $this->addFlash('error', 'Uploading failed. Please report back to our development team!');

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

                            // save in database
                            $em->persist($song);
                            $em->flush();
                            
                            return $this->redirectToRoute('song.detail', ['songId' => $song->getId()]);
                        } catch(\Exception $e) {
                            $this->addFlash('error', 'Uploading failed. Please report back to our development team!');
                        }
                    } catch(\Exception $e) {
                        $this->addFlash('error', 'Uploading failed. Please report back to our development team!');
                    }
                } else {
                    $this->addFlash('error', 'Uploading failed. Please report back to our development team!');
                }

            }
        }

        return $this->render('upload/index.html.twig', $tempVars);
    }
}
