<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;;
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
                        ->add('backupPath', FileType::class, ['label' => 'Backup .zip file'])
                        ->add('save', SubmitType::class, ['label' => 'Upload'])
                        ->getForm();
        $form->handleRequest($request);

        $tempVars['uploadForm'] = $form->createView();

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $backupFile = $data['backupPath'];

            if($backupFile) {
                $song->setFileReference(uniqid());

                $zip = new \ZipArchive;
                if($zip->open($backupFile)) {
                    try {
                        // extract backup
                        $extractionPath = $this->getParameter('temp_path').DIRECTORY_SEPARATOR.$song->getFileReference();
                        $zip->extractTo($extractionPath);
                        $zip->close();

                        try {
                            // get backup data
                            $allSRTBFiles = glob($extractionPath.DIRECTORY_SEPARATOR."*.srtb");
                            $srtbContent = json_decode(file_get_contents($allSRTBFiles[0]));
                            $trackInfo = json_decode($srtbContent->largeStringValuesContainer->values[0]->val);

                            // set meta data
                            $song->setTitle($trackInfo->title);
                            $song->setSubtitle($trackInfo->subtitle);
                            $song->setArtist($trackInfo->artistName);
                            $song->setAuthor($trackInfo->charter);

                            try {
                                // find cover
                                $coverFiles = glob($extractionPath.DIRECTORY_SEPARATOR."AlbumArt".DIRECTORY_SEPARATOR.$trackInfo->albumArtReference->assetName.".*");
                                if($coverFiles[0]) {
                                    rename($coverFiles[0], $this->getParameter('cover_path').DIRECTORY_SEPARATOR.$song->getFileReference().".png");
                                }
                            } catch(Exception $e) {
                                var_dump($e);
                            }


                            try {
                                // move backup file
                                $backupFile->move($this->getParameter('backup_path'), $song->getFileReference().'.'.$backupFile->guessExtension());
                            } catch(Exception $e) {
                                var_dump($e);
                            }

                            // clean up temp files
                            $hf = new HelperFunctions();
                            $hf->delTree($extractionPath);

                            // save in database
                            $em->persist($song);
                            $em->flush();

                            $tempVars['songInfo'] = $song;

                            return $this->render('upload/success.html.twig', $tempVars);
                        } catch(Exception $e) {
                            var_dump($e);
                        }
                    } catch(Exception $e) {
                        var_dump($e);
                    }
                } else {
                    throw new \Exception("Error when extracting ZIP file");
                }

            }
        }

        return $this->render('upload/index.html.twig', $tempVars);
    }
}
