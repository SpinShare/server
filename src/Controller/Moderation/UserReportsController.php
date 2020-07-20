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

class UserReportsController extends AbstractController
{

    /**
     * @Route("/moderation/reports/user/", name="moderation.reports.user.index")
     */
    public function reportsUserIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $openUserReports = $em->getRepository(UserReport::class)->findBy(array(), array('status' => 'ASC', 'reportDate' => 'DESC'));

        $data['userReports'] = $openUserReports;

        return $this->render('moderation/reports/userIndex.html.twig', $data);
    }

    /**
     * @Route("/moderation/reports/user/{reportId}", name="moderation.reports.user")
     */
    public function reportsUserDetail(Request $request, int $reportId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $report = $em->getRepository(UserReport::class)->findOneBy(array('id' => $reportId));
        $reportUser = $em->getRepository(User::class)->findOneBy(array('id' => $report->getUserId()));

        $data['report'] = $report;
        $data['reportUser'] = $reportUser;

        return $this->render('moderation/reports/user.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/reports/user/{reportId}/status/{newStatus}", name="moderation.reports.user.changeStatus")
     */
    public function reportsUserChangeStatus(Request $request, int $reportId, int $newStatus, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $reportToChange = $em->getRepository(UserReport::class)->findOneBy(array('id' => $reportId));

        try {
            $message = (new \Swift_Message('[#USER-'.$reportToChange->getId().'] Your reports status changed!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($reportToChange->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/changeStatus.txt.twig',
                                ['report' => $reportToChange, 'type' => 'USER']
                            ), 'text/plain');

            @$mailer->send($message);
        } catch ( \Exception $e ) { }

        $reportToChange->setStatus($newStatus);

        $em->persist($reportToChange);
        $em->flush();

        return $this->redirectToRoute('moderation.reports.user', array('reportId' => $reportId));
    }
}
