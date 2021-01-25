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
use App\Entity\Promo;

class ClientReleasesController extends AbstractController
{

    /**
     * @Route("/moderation/clientReleases/", name="moderation.clientreleases.index")
     */
    public function clientReleasesIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $clientReleases = $em->getRepository(ClientRelease::class)->findBy(array(), array('uploadDate' => 'DESC'));
        $data['clientReleases'] = $clientReleases;

        return $this->render('moderation/clientReleases/index.html.twig', $data);
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
                        ->add('platform', ChoiceType::class, ['label' => 'Platform', 'choices' => array('Windows' => 'win32', 'Mac' => 'darwin')])
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

                $fileEnding = "";
                switch($data['platform']) {
                    case "win32":
                        $fileEnding = ".exe";
                        break;
                    case "darwin":
                        $fileEnding = ".dmg";
                        break;
                }

                $newFilename = "SpinShare_".$data['platform']."_".$data['majorVersion']."_".$data['minorVersion']."_".$data['patchVersion'].$fileEnding;
                $data['executableFile']->move($this->getParameter('client_path'), $newFilename);

                $newRelease->setFileReference($newFilename);

                $em->persist($newRelease);
                $em->flush();

                return $this->redirectToRoute('moderation.clientreleases.index');
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

        return $this->redirectToRoute('moderation.clientreleases.index');
    }
}
