<?php

namespace App\Controller;

use App\Entity\SongPlaylist;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use App\Entity\Song;
use App\Entity\SongReview;
use App\Entity\SongSpinPlay;
use App\Utils\HelperFunctions;
use App\Entity\User;
use App\Entity\UserNotification;

class PlaylistController extends AbstractController
{
    /**
     * @Route("/playlist/create", name="playlist.create")
     * @param Request $request
     * @param int $playlistId
     * @return RedirectResponse|Response
     */
    public function playlistCreate(Request $request)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];
        $data['formData'] = "";

        $playlist = new SongPlaylist();

        $form = $this->createFormBuilder()
            ->add('title', TextType::class, ['label' => 'Title', 'row_attr' => array('class' => 'tags-field'), 'required' => true])
            ->add('description', TextareaType::class, ['label' => 'Description', 'attr' => array('rows' => 5), 'row_attr' => array('class' => 'tags-field'), 'required' => false])
            ->add('coverPath', FileType::class, ['label' => 'Cover Image', 'row_attr' => array('class' => 'upload-field'), 'attr' => array('accept' => '.png, .jpg, .jpeg')])
            ->add('save', SubmitType::class, ['label' => 'Create'])
            ->getForm();
        $form->handleRequest($request);

        $data['form'] = $form->createView();

        if($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $coverFile = $formData['coverPath'];

            if($coverFile) {
                try {
                    $playlist->setTitle($formData['title']);
                    $playlist->setDescription($formData['description']);
                    $playlist->setFileReference("playlist_" . uniqid());
                    $playlist->setUser($this->getUser());
                    $playlist->setIsOfficial(false);

                    rename($coverFile, $this->getParameter('cover_path') . DIRECTORY_SEPARATOR . $playlist->getFileReference() . ".png");

                    $em->persist($playlist);
                    $em->flush();

                    return $this->redirectToRoute('playlist.detail', ['playlistId' => $playlist->getId()]);
                } catch(Exception $e) {
                    $this->addFlash('error', 'Creating failed. Please report back to our development team!');

                    var_dump($e);

                    return $this->render('playlist/create.html.twig', $data);
                }
            }
        }

        return $this->render('playlist/create.html.twig', $data);
    }

    /**
     * @Route("/playlist/{playlistId}", name="playlist.detail")
     * @param Request $request
     * @param int $playlistId
     * @return RedirectResponse|Response
     */
    public function playlistDetail(Request $request, int $playlistId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $resultPlaylist = $em->getRepository(SongPlaylist::class)->findOneBy(array('id' => $playlistId));

        if(!$resultPlaylist) throw new NotFoundHttpException();

        $resultUser = $em->getRepository(User::class)->findOneBy(array('id' => $resultPlaylist->getUser()));
        if(!$resultUser) throw new NotFoundHttpException();

        $data['playlist'] = $resultPlaylist;
        $data['playlistCount'] = count($resultPlaylist->getSongs());
        $data['user'] = $resultUser;

        return $this->render('playlist/detail.html.twig', $data);
    }

    /**
     * @Route("/playlist/{playlistId}/delete", name="playlist.delete")
     * @param Request $request
     * @param int $playlistId
     * @return RedirectResponse|Response
     */
    public function playlistDelete(Request $request, int $playlistId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $resultPlaylist = $em->getRepository(Song::class)->findOneBy(array('id' => $playlistId, 'user' => $user->getId()));
        $data['playlist'] = $resultPlaylist;

        if(!$resultPlaylist) {
            throw new NotFoundHttpException();
        } else {
            if($request->query->get('isConfirmed')) {
                // remove the entity
                $em->remove($resultPlaylist);
                $em->flush();

                // Redirect
                return $this->redirectToRoute('user.detail', ['userId' => $user->getId(), 'area' => 'playlists']);
            } else {
                return $this->render('playlist/delete.html.twig', $data);
            }
        }
    }

    /**
     * @Route("/playlist/{playlistId}/song/{songId}/delete", name="playlist.song.delete")
     * @param Request $request
     * @param int $playlistId
     * @param int $songId
     * @return RedirectResponse
     */
    public function playlistSongDelete(Request $request, int $playlistId, int $songId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $resultPlaylist = $em->getRepository(SongPlaylist::class)->findOneBy(array('id' => $playlistId));
        $resultSong = $em->getRepository(Song::class)->findOneBy(array('id' => $songId));

        if(!$resultPlaylist || !$resultSong) {
            return $this->redirectToRoute('playlist.detail', ['playlistId' => $playlistId]);
        } else {
            // TODO
            $userRoles = $this->getUser()->getRoles();
            $allowedRoles = ["ROLE_ADMIN", "ROLE_SUPERADMIN", "ROLE_MODERATOR"];

            // Check if allowed to remove
            if(count(array_intersect($allowedRoles, $userRoles)) > 0 || $resultPlaylist->getUser() == $this->getUser()) {
                $resultPlaylist->removeSong($resultSong);
                $em->persist($resultPlaylist);
                $em->flush();
            }

            // Redirect
            return $this->redirectToRoute('playlist.detail', ['playlistId' => $playlistId]);
        }
    }
}
