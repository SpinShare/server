<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use App\Entity\ClientRelease;
use App\Entity\Song;
use App\Entity\SongReport;
use App\Entity\User;
use App\Entity\UserReport;
use App\Entity\Promo;

class ReportController extends AbstractController
{
    /**
     * @Route("/report/user/{userId}", name="report.user")
     */
    public function reportUser(Request $request, int $userId)
    {
        $em = $this->getDoctrine()->getManager();
        $tempVars = [];

        $userToReport = $em->getRepository(User::class)->findOneBy(array('id' => $userId));
        if(!$userToReport) { throw new NotFoundHttpException(); }

        $tempVars['userToReport'] = $userToReport;

        $form = $this->createFormBuilder()
                        ->add('reportReason', ChoiceType::class, ['label' => 'Reason', 'row_attr' => array('class' => 'tags-field'), 'choices'  => [
                            'This user posts personal and/or confidential information' => 'personal',
                            'This user posts sexual or suggestive content involving minors' => 'sexualcontent',
                            'This user posts spam' => 'spam',
                            'This user evades a ban' => 'banevasion',
                            'This user impersonates another person' => 'impersonation',
                            'Other reason' => 'other',
                        ]])
                        ->add('reportText', TextareaType::class, ['label' => 'Explain the situation', 'row_attr' => array('class' => "tags-field"), 'attr' => array('rows' => 5)])
                        ->add('reportName', TextType::class, ['label' => 'Your name', 'row_attr' => array('class' => "tags-field")])
                        ->add('reportEmail', TextType::class, ['label' => 'Your email', 'row_attr' => array('class' => "tags-field")])
                        ->add('reportProof', TextType::class, ['label' => 'Additional documents', 'row_attr' => array('class' => "tags-field"), 'required' => false])
                        ->add('save', SubmitType::class, ['label' => 'Report'])
                        ->getForm();
        $form->handleRequest($request);

        $tempVars['reportForm'] = $form->createView();

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $newReport = new UserReport();
            $newReport->setUserId($userToReport->getId());
            $newReport->setReason($data['reportReason']);
            $newReport->setText($data['reportText']);
            $newReport->setName($data['reportName']);
            $newReport->setEmail($data['reportEmail']);
            $newReport->setProof($data['reportProof']);
            $newReport->setStatus(0);
            $newReport->setReportDate(new \DateTime());

            $em->persist($newReport);
            $em->flush();

            return $this->render('report/confirmed.html.twig');
        }

        return $this->render('report/user.html.twig', $tempVars);
    }

    /**
     * @Route("/report/song/{songId}", name="report.song")
     */
    public function reportSong(Request $request, int $songId)
    {
        $em = $this->getDoctrine()->getManager();
        $tempVars = [];

        $songToReport = $em->getRepository(Song::class)->findOneBy(array('id' => $songId));
        if(!$songToReport) { throw new NotFoundHttpException(); }

        $tempVars['songToReport'] = $songToReport;

        $form = $this->createFormBuilder()
                        ->add('reportReason', ChoiceType::class, ['label' => 'Reason', 'row_attr' => array('class' => 'tags-field'), 'choices'  => [
                            'This song contains my intellectual property (DMCA Takedown)' => 'dmca',
                            'This song is broken' => 'broken',
                            'This song is spam' => 'spam',
                            'This song has wrong meta data' => 'metadata',
                            'Other reason' => 'other',
                        ]])
                        ->add('reportText', TextareaType::class, ['label' => 'Explain the situation', 'row_attr' => array('class' => "tags-field"), 'attr' => array('rows' => 5)])
                        ->add('reportName', TextType::class, ['label' => 'Your name', 'row_attr' => array('class' => "tags-field")])
                        ->add('reportEmail', TextType::class, ['label' => 'Your email', 'row_attr' => array('class' => "tags-field")])
                        ->add('reportProof', TextType::class, ['label' => 'Additional documents', 'row_attr' => array('class' => "tags-field"), 'required' => false])
                        ->add('save', SubmitType::class, ['label' => 'Report'])
                        ->getForm();
        $form->handleRequest($request);

        $tempVars['reportForm'] = $form->createView();

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $newReport = new SongReport();
            $newReport->setSongId($songToReport->getId());
            $newReport->setReason($data['reportReason']);
            $newReport->setText($data['reportText']);
            $newReport->setName($data['reportName']);
            $newReport->setEmail($data['reportEmail']);
            $newReport->setProof($data['reportProof']);
            $newReport->setStatus(0);
            $newReport->setReportDate(new \DateTime());

            $em->persist($newReport);
            $em->flush();

            return $this->render('report/confirmed.html.twig');
        }

        return $this->render('report/song.html.twig', $tempVars);
    }
}
