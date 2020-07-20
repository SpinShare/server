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

use App\Entity\Song;
use App\Entity\User;
use App\Entity\SongReport;
use App\Entity\UserReport;

class SongReportsController extends AbstractController
{

    /**
     * @Route("/moderation/reports/song/", name="moderation.reports.song.index")
     */
    public function reportsSongIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $openSongReports = $em->getRepository(SongReport::class)->findBy(array(), array('status' => 'ASC', 'reportDate' => 'DESC'));

        $data['songReports'] = $openSongReports;

        return $this->render('moderation/reports/songIndex.html.twig', $data);
    }

    /**
     * @Route("/moderation/reports/song/{reportId}", name="moderation.reports.song")
     */
    public function reportsSongDetail(Request $request, int $reportId)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $report = $em->getRepository(SongReport::class)->findOneBy(array('id' => $reportId));
        $data['report'] = $report;

        $reportSong = $em->getRepository(Song::class)->findOneBy(array('id' => $report->getSongId()));
        $data['reportSong'] = $reportSong;

        if($reportSong) {
            $reportSongUploader = $em->getRepository(User::class)->findOneBy(array('id' => $reportSong->getUploader()));
            $data['reportSongUploader'] = $reportSongUploader;
        }

        return $this->render('moderation/reports/song.html.twig', $data);
    }
    
    /**
     * @Route("/moderation/reports/song/{reportId}/status/{newStatus}", name="moderation.reports.song.changeStatus")
     */
    public function reportsSongChangeStatus(Request $request, int $reportId, int $newStatus, \Swift_Mailer $mailer)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $reportToChange = $em->getRepository(SongReport::class)->findOneBy(array('id' => $reportId));

        try {
            $message = (new \Swift_Message('[#SONG-'.$reportToChange->getId().'] Your reports status changed!'))
                        ->setFrom('legal@spinsha.re')
                        ->setTo($reportToChange->getEmail())
                        ->setBody(
                            $this->renderView(
                                'emails/moderation/changeStatus.txt.twig',
                                ['report' => $reportToChange, 'type' => 'SONG']
                            ), 'text/plain');

            @$mailer->send($message);
        } catch ( \Exception $e ) { }

        $reportToChange->setStatus($newStatus);

        $em->persist($reportToChange);
        $em->flush();

        return $this->redirectToRoute('moderation.reports.song', array('reportId' => $reportId));
    }
}
