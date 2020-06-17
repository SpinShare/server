<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Entity\User;
use App\Entity\Song;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{userId}", name="user.detail")
     */
    public function userDetail(Request $request, int $userId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        if($user) {
            if($user->getId() == $userId) {
                if($request->request->get('save') == "changeAvatar") {
                    if($request->files->get('newAvatar')) {
                        $allowedMimeTypes = array("image/jpeg", "image/png");

                        if(in_array($request->files->get('newAvatar')->getMimeType(), $allowedMimeTypes)) {
                            @unlink($this->getParameter('avatar_path').DIRECTORY_SEPARATOR.$user->getCoverReference());

                            $user->setCoverReference(uniqid().".png");
                            rename($request->files->get('newAvatar'), $this->getParameter('avatar_path').DIRECTORY_SEPARATOR.$user->getCoverReference());

                            $em->persist($user);
                            $em->flush();
                        }
                    }
                }
            }
    }

        $resultUser = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        if(!$resultUser) throw new NotFoundHttpException();

        $resultUploads = $em->getRepository(Song::class)->findBy(array('uploader' => $resultUser->getId()), array('uploadDate' => 'DESC'));

        $data['user'] = $resultUser;
        $data['uploads'] = $resultUploads;

        return $this->render('user/detail.html.twig', $data);
    }
}
