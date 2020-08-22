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

use App\Entity\Card;
use App\Entity\User;
use App\Entity\UserCard;
use App\Entity\UserNotification;

class CardsController extends AbstractController
{
    /**
     * @Route("/moderation/cards/", name="moderation.cards.index")
     */
    public function cardsIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $cards = $em->getRepository(Card::class)->findBy(array(), array('id' => 'DESC'));
        $data['cards'] = $cards;

        return $this->render('moderation/cards/index.html.twig', $data);
    }
    /**
     * @Route("/moderation/cards/give/{cardId}", name="moderation.cards.give")
     */
    public function cardsGive(Request $request, int $cardId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $card = $em->getRepository(Card::class)->findOneBy(array('id' => $cardId));
        $data['card'] = $card;

        $users = $em->getRepository(User::class)->findAll();

        $allUsers = [];
        foreach($users as $user) {
            $allUsers[$user->getId()] = $user->getUsername();
        }

        $data['allUsers'] = $allUsers;

        if($request->request->get('saveSingle') == "Give") {
            $user = $em->getRepository(User::class)->findOneBy(array('id' => $request->request->get('userId')));

            $existingUserCard = $em->getRepository(UserCard::class)->findOneBy(array('card' => $card,'user' => $user));

            if($existingUserCard == null && $user != null) {
                $newUserCard = new UserCard();
                $newUserCard->setCard($card);
                $newUserCard->setUser($user);
                $newUserCard->setGivenDate(new \DateTime());

                $em->persist($newUserCard);

                $newNotification = new UserNotification();
                $newNotification->setUser($user);
                $newNotification->setNotificationType(3);
                $newNotification->setNotificationData("");
                $newNotification->setConnectedCard($newUserCard->getCard());
                $newNotification->setConnectedUser($user);
    
                $em->persist($newNotification);
                $em->flush();
            }

            return $this->redirectToRoute('moderation.cards.index');
        }

        if($request->request->get('saveMulti') == "Give") {
            $userIds = explode(",", $request->request->get('userId'));

            foreach($userIds as $userId) {
                $user = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
                
                $existingUserCard = $em->getRepository(UserCard::class)->findOneBy(array('card' => $card,'user' => $user));

                if($existingUserCard == null && $user != null) {
                    $newUserCard = new UserCard();
                    $newUserCard->setCard($card);
                    $newUserCard->setUser($user);
                    $newUserCard->setGivenDate(new \DateTime());
        
                    $em->persist($newUserCard);
                    $em->flush();
                }

                return $this->redirectToRoute('moderation.cards.index');
            }
        }

        return $this->render('moderation/cards/give.html.twig', $data);
    }

    /**
     * @Route("/moderation/cards/add", name="moderation.cards.add")
     */
    public function cardsAdd(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $form = $this->createFormBuilder()
                        ->add('iconFile', FileType::class, ['label' => 'Icon File', 'attr' => array('accept' => '.png')])
                        ->add('title', TextType::class, ['label' => 'Title'])
                        ->add('description', TextType::class, ['label' => 'Description'])
                        ->add('save', SubmitType::class, ['label' => 'Create'])
                        ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $newCard = new Card();
                $newCard->setTitle($data['title']);
                $newCard->setDescription($data['description']);

                $newFilename = "card_".md5($newCard->getTitle()).".png";
                $data['iconFile']->move($this->getParameter('card_path'), $newFilename);

                $newCard->setIcon($newFilename);

                $em->persist($newCard);
                $em->flush();

                return $this->redirectToRoute('moderation.cards.index');
            } catch(FileException $e) {

            }
        }

        $data['addForm'] = $form->createView();

        return $this->render('moderation/cards/add.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/cards/edit/{cardId}", name="moderation.cards.edit")
     */
    public function cardsEdit(Request $request, int $cardId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $cardToEdit = $em->getRepository(Card::class)->findOneBy(array('id' => $cardId));

        $form = $this->createFormBuilder()
                        ->add('iconFile', FileType::class, ['label' => 'Icon File', 'attr' => array('accept' => '.png'), 'required' => false])
                        ->add('title', TextType::class, ['label' => 'Title', 'data' => $cardToEdit->getTitle()])
                        ->add('description', TextType::class, ['label' => 'Description', 'data' => $cardToEdit->getDescription()])
                        ->add('save', SubmitType::class, ['label' => 'Save'])
                        ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            try {
                $cardToEdit->setTitle($data['title']);
                $cardToEdit->setDescription($data['description']);

                if($data['iconFile'] != null) {
                    try {
                        $fileToRemove = glob($this->getParameter('card_path').DIRECTORY_SEPARATOR.$cardToEdit->getIcon());
                        if(count($fileToRemove) > 0) {
                            @unlink($fileToRemove[0]);
                        }
                    } catch(FileException $e) {
            
                    }

                    $newFilename = "card_".md5($cardToEdit->getTitle()).".png";
                    $data['iconFile']->move($this->getParameter('card_path'), $newFilename);

                    $cardToEdit->setIcon($newFilename);
                }

                $em->persist($cardToEdit);
                $em->flush();

                return $this->redirectToRoute('moderation.cards.index');
            } catch(FileException $e) {

            }
        }

        $data['editForm'] = $form->createView();

        return $this->render('moderation/cards/edit.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/cards/remove/{cardId}", name="moderation.cards.remove")
     */
    public function cardsRemove(Request $request, int $cardId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $cardToRemove = $em->getRepository(Card::class)->findOneBy(array('id' => $cardId));
        
        // Remove Icon file
        try {
            $fileToRemove = glob($this->getParameter('card_path').DIRECTORY_SEPARATOR.$cardToRemove->getIcon());
            if(count($fileToRemove) > 0) {
                @unlink($fileToRemove[0]);
            }
        } catch(FileException $e) {

        }

        // Remove Entity
        $em->remove($cardToRemove);
        $em->flush();

        return $this->redirectToRoute('moderation.cards.index');
    }
}
