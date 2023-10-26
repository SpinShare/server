<?php

namespace App\Controller\Moderation;

use App\Entity\DLC;
use App\Entity\DLCHash;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class DLCController extends AbstractController
{
    /**
     * @Route("/moderation/dlcs/", name="moderation.dlcs.index")
     */
    public function dlcsIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $dlcs = $em->getRepository(DLC::class)->findBy(array(), array('id' => 'DESC'));
        $data['dlcs'] = $dlcs;

        return $this->render('moderation/dlcs/index.html.twig', $data);
    }

    /**
     * @Route("/moderation/dlcs/add", name="moderation.dlcs.add")
     */
    public function dlcsAdd(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $form = $this->createFormBuilder()
            ->add('identifier', TextType::class, ['label' => 'DLC Identifier'])
            ->add('title', TextType::class, ['label' => 'DLC Title'])
            ->add('storeLink', TextType::class, ['label' => 'DLC Store Link'])
            ->add('save', SubmitType::class, ['label' => 'Create'])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $newDLC = new DLC();
                $newDLC->setIdentifier($data['identifier']);
                $newDLC->setTitle($data['title']);
                $newDLC->setStoreLink($data['storeLink']);

                $em->persist($newDLC);
                $em->flush();

                return $this->redirectToRoute('moderation.dlcs.index');
            } catch(FileException $e) {

            }
        }

        $data['addForm'] = $form->createView();

        return $this->render('moderation/dlcs/add.html.twig', $data);
    }

    /**
     * @Route("/moderation/dlcs/edit/{dlcId}", name="moderation.dlcs.edit")
     */
    public function dlcsEdit(Request $request, int $dlcId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $dlcToEdit = $em->getRepository(DLC::class)->findOneBy(array('id' => $dlcId));

        $form = $this->createFormBuilder()
            ->add('identifier', TextType::class, ['label' => 'DLC Identifier', 'data' => $dlcToEdit->getIdentifier()])
            ->add('title', TextType::class, ['label' => 'DLC Title', 'data' => $dlcToEdit->getTitle()])
            ->add('storeLink', TextType::class, ['label' => 'DLC Store Link', 'data' => $dlcToEdit->getStoreLink()])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $dlcToEdit->setIdentifier($data['identifier']);
                $dlcToEdit->setTitle($data['title']);
                $dlcToEdit->setStoreLink($data['storeLink']);

                $em->persist($dlcToEdit);
                $em->flush();

                return $this->redirectToRoute('moderation.dlcs.index');
            } catch(FileException $e) {

            }
        }

        $data['editForm'] = $form->createView();

        return $this->render('moderation/dlcs/edit.html.twig', $data);
    }

    /**
     * @Route("/moderation/dlcs/hashes/{dlcId}", name="moderation.dlcs.hashes")
     */
    public function dlcsHashes(Request $request, int $dlcId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $dlc = $em->getRepository(DLC::class)->findOneBy(array('id' => $dlcId));

        $form = $this->createFormBuilder()
            ->add('hash', TextareaType::class, ['label' => 'Comma Seperated Hashes'])
            ->add('save', SubmitType::class, ['label' => 'Save'])
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $commaSeperatedHashes = explode(",", $data['hash']);

            foreach($commaSeperatedHashes as $hash) {
                $existingHash = $em->getRepository(DLCHash::class)->findOneBy(array('hash' => trim($hash)));
                if($existingHash != null) continue;

                $newHash = new DLCHash();
                $newHash->setDLC($dlc);
                $newHash->setHash(trim($hash));

                $em->persist($newHash);
            }
            $em->flush();

            return $this->redirectToRoute('moderation.dlcs.hashes', [
                'dlcId' => $dlcId,
            ]);
        }

        $data['dlc'] = $dlc;
        $data['addHashesForm'] = $form->createView();

        return $this->render('moderation/dlcs/hashes.html.twig', $data);
    }

    /**
     * @Route("/moderation/dlcs/{dlcId}/hashes/{hashId}/remove", name="moderation.dlcs.hashes.remove")
     */
    public function dlcsHashRemove(Request $request, int $dlcId, int $hashId): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $dlcHashId = $em->getRepository(DLCHash::class)->findOneBy(array('id' => $hashId));

        // Remove Entity
        $em->remove($dlcHashId);
        $em->flush();

        return $this->redirectToRoute('moderation.dlcs.hashes', [
            'dlcId' => $dlcId,
        ]);
    }

    /**
     * @Route("/moderation/dlcs/remove/{dlcId}", name="moderation.dlcs.remove")
     */
    public function dlcsRemove(Request $request, int $dlcId): RedirectResponse
    {
        $em = $this->getDoctrine()->getManager();

        $dlcToRemove = $em->getRepository(DLC::class)->findOneBy(array('id' => $dlcId));

        // Remove Entity
        $em->remove($dlcToRemove);
        $em->flush();

        return $this->redirectToRoute('moderation.dlcs.index');
    }
}
