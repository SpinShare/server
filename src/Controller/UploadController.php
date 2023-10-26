<?php

namespace App\Controller;

use App\Entity\DLC;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Song;
use App\Utils\HelperFunctions;
use ZipArchive;

class UploadController extends AbstractController
{
    /**
     * @Route("/upload", name="upload.index")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tempVars = [];

        $dlcs = $em->getRepository(DLC::class)->findAll();
        $dlcOptions = [
            "None" => 0
        ];
        foreach($dlcs as $dlc) {
            $dlcOptions[$dlc->getTitle()] = $dlc->getId();
        }

        $form = $this->createFormBuilder()
                        ->add('backupPath', FileType::class, ['label' => 'Backup .zip', 'row_attr' => array('class' => 'upload-field'), 'attr' => array('accept' => '.zip')])
                        ->add('tags', TextType::class, ['label' => 'Tags', 'row_attr' => array('class' => 'tags-field'), 'required' => false])
                        ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => array('rows' => 5), 'row_attr' => array('class' => 'tags-field'), 'required' => false])
                        ->add('isExplicit', CheckboxType::class, ['label' => 'Is the song explicit?', 'row_attr' => array('class' => "tags-field"), 'required' => false])
                        ->add('dlc', ChoiceType::class, ['label' => 'DLC', 'row_attr' => array('class' => "tags-field"), 'required' => true, 'choices' => $dlcOptions])
                        ->add('publicationStatus', ChoiceType::class, ['label' => 'Publication Status', 'row_attr' => array('class' => "tags-field"), 'required' => true, 'choices' => ['Public' => 0, 'Hide from lists' => 1, 'Unlisted' => 2]])
                        ->add('save', SubmitType::class, ['label' => 'Upload'])
                        ->getForm();
        $form->handleRequest($request);

        $tempVars['uploadForm'] = $form->createView();

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $song = new Song();
            $backupFile = $data['backupPath'];

            if($backupFile) {
                $song->setFileReference("spinshare_".uniqid());
                $song->setTags($data['tags']);
                $song->setDescription($data['description']);
                $song->setUploadDate(new \DateTime('NOW'));
                $song->setIsExplicit($data['isExplicit']);
                $song->setPublicationStatus($data['publicationStatus']);

                if($data['dlc'] !== null && $data['dlc'] !== 0) {
                    $dlc = $em->getRepository(DLC::class)->find($data['dlc']);
                    $song->setDLC($dlc);
                }

                $zip = new ZipArchive;
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

                                // Detect used difficulties
                                foreach($trackInfo->difficulties as $oneData) {
                                    if(isset($oneData->_active) && $oneData->_active || !isset($oneData->_active)) {
                                        $assetIndex = str_replace("TrackData_", "", $oneData->assetName);

                                        switch($trackData[$assetIndex]->difficultyType) {
                                            case 2:
                                                $song->setHasEasyDifficulty(true);
                                                $song->setEasyDifficulty($trackData[$assetIndex]->difficultyRating);
                                                break;
                                            case 3:
                                                $song->setHasNormalDifficulty(true);
                                                $song->setNormalDifficulty($trackData[$assetIndex]->difficultyRating);
                                                break;
                                            case 4:
                                                $song->setHasHardDifficulty(true);
                                                $song->setHardDifficulty($trackData[$assetIndex]->difficultyRating);
                                                break;
                                            case 5:
                                                $song->setHasExtremeDifficulty(true);
                                                $song->setExpertDifficulty($trackData[$assetIndex]->difficultyRating);
                                                break;
                                            case 6:
                                                $song->setHasXDDifficulty(true);
                                                $song->setXDDifficulty($trackData[$assetIndex]->difficultyRating);
                                                break;
                                        }
                                    }
                                }
                            } catch(Exception $e) {
                                $this->addFlash('error', 'Couldn\'t extract backup. Please report back to our development team!');

                                // clean up temp files
                                $hf = new HelperFunctions();
                                $hf->delTree($extractionPath);

                                return $this->render('upload/index.html.twig', $tempVars);
                            }

                            try {
                                // find cover
                                $coverFiles = glob($extractionPath.DIRECTORY_SEPARATOR."AlbumArt".DIRECTORY_SEPARATOR.$trackInfo->albumArtReference->assetName.".*");
                                if(count($coverFiles) > 0) {
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
                                $this->addFlash('error', 'Couldn\'t save AlbumArt. Please report back to our development team!');

                                // clean up temp files
                                $hf = new HelperFunctions();
                                $hf->delTree($extractionPath);

                                return $this->render('upload/index.html.twig', $tempVars);
                            }

                            try {
                                foreach($clipInfo as $clipIndex => $clipItem) {
                                    // find audio
                                    $assetName = $clipItem->clipAssetReference->assetName;
                                    $newAssetName = $song->getFileReference()."_".$clipIndex;
                                    $oggLocation = $extractionPath.DIRECTORY_SEPARATOR."AudioClips".DIRECTORY_SEPARATOR.$assetName.".ogg";
                                    $mp3Location = $extractionPath.DIRECTORY_SEPARATOR."AudioClips".DIRECTORY_SEPARATOR.$assetName.".mp3";
                                    if(is_file($oggLocation)) {
                                        $clipInfo[$clipIndex]->clipAssetReference->assetName = $newAssetName;
                                        rename($oggLocation, $this->getParameter('audio_path').DIRECTORY_SEPARATOR.$newAssetName.".ogg");
                                    }
                                    if(is_file($mp3Location)) {
                                        $clipInfo[$clipIndex]->clipAssetReference->assetName = $newAssetName;
                                        rename($mp3Location, $this->getParameter('audio_path').DIRECTORY_SEPARATOR.$newAssetName.".mp3");
                                    }
                                }
                            } catch(Exception $e) {
                                $this->addFlash('error', 'Couldn\'t save AudioClips. Please report back to our development team!');

                                // clean up temp files
                                $hf = new HelperFunctions();
                                $hf->delTree($extractionPath);

                                return $this->render('upload/index.html.twig', $tempVars);
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
                            
                            // Generate new UpdateHash
                            $song->setUpdateHash(md5(json_encode($srtbContent)));

                            // clean up temp files
                            $hf = new HelperFunctions();
                            $hf->delTree($extractionPath);

                            // save in database
                            $em->persist($song);
                            $em->flush();

                            return $this->redirectToRoute('song.detail', ['songId' => $song->getId()]);
                        } catch(\Exception $e) {
                            $this->addFlash('error', 'Couldn\'t save SRTB & database entry. Please report back to our development team!');

                            return $this->render('upload/index.html.twig', $tempVars);
                        }
                    } catch(\Exception $e) {
                        $this->addFlash('error', 'Uploading failed. Please report back to our development team!');

                        return $this->render('upload/index.html.twig', $tempVars);
                    }
                } else {
                    $this->addFlash('error', 'Uploading failed. Please report back to our development team!');
                }

            }
        }

        return $this->render('upload/index.html.twig', $tempVars);
    }
}
