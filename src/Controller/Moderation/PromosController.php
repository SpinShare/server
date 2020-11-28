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

use App\Entity\Promo;

class PromosController extends AbstractController
{
    /**
     * @Route("/moderation/promos/", name="moderation.promos.index")
     */
    public function promosIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $promos = $em->getRepository(Promo::class)->findBy(array(), array('id' => 'DESC'));
        $data['promos'] = $promos;

        return $this->render('moderation/promos/index.html.twig', $data);
    }

    /**
     * @Route("/moderation/promos/add", name="moderation.promos.add")
     */
    public function promosAdd(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $form = $this->createFormBuilder()
                        ->add('imagePath', FileType::class, ['label' => 'Banner File (550x256px)', 'attr' => array('accept' => '.png, .jpg, .jpeg')])
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

                return $this->redirectToRoute('moderation.promos.index');
            } catch(FileException $e) {

            }
        }

        $data['addForm'] = $form->createView();

        return $this->render('moderation/promos/add.html.twig', $data);
    }

    /**
     * @Route("/moderation/promos/edit/{promoId}", name="moderation.promos.edit")
     */
    public function promosEdit(Request $request, int $promoId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $promo = $em->getRepository(Promo::class)->findOneBy(array('id' => $promoId));

        $form = $this->createFormBuilder()
                        ->add('imagePath', FileType::class, ['label' => 'New Banner File (550x256px)', 'attr' => array('accept' => '.png, .jpg, .jpeg'), 'required' => false])
                        ->add('title', TextType::class, ['label' => 'Title', 'data' => $promo->getTitle()])
                        ->add('type', TextType::class, ['label' => 'Type', 'data' => $promo->getType()])
                        ->add('textColor', TextType::class, ['label' => 'Text Color', 'data' => $promo->getTextColor()])
                        ->add('color', TextType::class, ['label' => 'Primary Color', 'data' => $promo->getColor()])
                        ->add('buttonType', ChoiceType::class, ['label' => 'Button Type', 'choices' => array('Song' => 0, 'Playlist (Unused)' => 1, 'Search Query' => 2, 'External' => 3), 'data' => $promo->getButtonType()])
                        ->add('buttonData', TextType::class, ['label' => 'Button Data', 'data' => $promo->getButtonData()])
                        ->add('isVisible', ChoiceType::class, ['label' => 'Is Visible?', 'choices' => array('Yes' => true, 'No' => false), 'data' => $promo->getIsVisible()])
                        ->add('save', SubmitType::class, ['label' => 'Save'])
                        ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $promo->setTitle($data['title']);
                $promo->setType($data['type']);
                $promo->setTextColor($data['textColor']);
                $promo->setColor($data['color']);
                $promo->setButtonType($data['buttonType']);
                $promo->setButtonData($data['buttonData']);
                $promo->setIsVisible($data['isVisible']);

                if($data['imagePath'] != null) {
                    try {
                        $fileToRemove = glob($this->getParameter('promo_path').DIRECTORY_SEPARATOR.$promo->getImagePath());
                        if(count($fileToRemove) > 0) {
                            @unlink($fileToRemove[0]);
                        }
                    } catch(FileException $e) {
            
                    }

                    $newFilename = "promo_".uniqid().".png";
                    $data['imagePath']->move($this->getParameter('promo_path'), $newFilename);

                    $promo->setImagePath($newFilename);
                }

                $em->persist($promo);
                $em->flush();

                return $this->redirectToRoute('moderation.promos.index');
            } catch(FileException $e) {

            }
        }

        $data['editForm'] = $form->createView();

        return $this->render('moderation/promos/edit.html.twig', $data);
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

        return $this->redirectToRoute('moderation.promos.index');
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

        return $this->redirectToRoute('moderation.promos.index');
    }
}
