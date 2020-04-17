<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use FOS\UserBundle\Model\UserManagerInterface;

use App\Entity\User;

class UserSettingsController extends AbstractController
{
    /**
     * @Route("/settings", name="user.settings")
     */
    public function userSettings(Request $request, UserPasswordEncoderInterface $encoder, UserManagerInterface $userManager)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $data['my'] = $user;

        if($request->request->get('save')) {
            $formData = $request->request;

            switch($request->request->get('save')) {
                case "general":
                    if($formData->get('email') != "" && $formData->get('username')) {
                        $existingUser = $em->getRepository(User::class)->findOneBy(array('username' => $formData->get('username'), 'email' => $formData->get('email')));

                        if($existingUser) {
                            $user->setEmail($formData->get('email'));
                            $user->setUsername($formData->get('username'));
                            $this->addFlash('success', 'Saved successfully!');
                        } else {
                            $this->addFlash('error', 'Email or username are already in use!');
                        }
                    } else {
                        $this->addFlash('error', 'Email and username can\'t be empty!');
                    }
                break;
                case "login":
                    if($formData->get('newPassword') == $formData->get('newPasswordAgain')) {
                        $user->setPlainPassword($formData->get('newPassword'));
                        $this->addFlash('success', 'Saved successfully!');
                    } else {
                        $this->addFlash('error', 'Passwords must be equal!');
                    }
                break;
            }

            $userManager->updateUser($user);
        }

        return $this->render('user/settings.html.twig', $data);
    }
}
