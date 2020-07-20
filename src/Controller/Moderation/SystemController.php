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
     * @Route("/moderation/system/deploy", name="moderation.system.deploy")
     */
    public function systemDeploy(Request $request)
    {
        shell_exec("bash /var/www/spinshare/pull-volatile.sh");

        return $this->redirectToRoute('moderation.system.index');
    }
    
    /**
     * @Route("/moderation/system/rollback", name="moderation.system.rollback")
     */
    public function systemRollback(Request $request)
    {
        shell_exec("dep rollback");

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

        foreach($allSongs as $oneSong) {
            $filePath = glob($this->getParameter('cover_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".png");
            if(count($filePath) > 0) {
                $hf = new HelperFunctions();
                $hf->generateThumbnail($filePath[0], $this->getParameter('thumbnail_path').DIRECTORY_SEPARATOR.$oneSong->getFileReference().".jpg", 300);
            }
        }

        return $this->redirectToRoute('moderation.system.index');
    }
}
