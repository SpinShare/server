<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Utils\HelperFunctions;

use App\Entity\Song;
use App\Entity\User;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search.index")
     */
    public function searchIndex(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $data = [];

        $searchQuery = $request->query->get('q');

        $resultsUsers = [];
        $resultsSongs = [];

        if($request->query->get('showAll') == "1") {
            $resultsSongs = $em->getRepository(Song::class)->findBy(array('publicationStatus' => array(0, 1)), array('id' => 'DESC'));

            $data['results']['users'] = $resultsUsers;
            $data['results']['songs'] = $resultsSongs;

            $filterEasy = true;
            $filterNormal = true;
            $filterHard = true;
            $filterExpert = true;
            $filterXD = true;
    
            $filterMinDifficulty = 0;
            $filterMaxDifficulty = 99;
        } else {
            if($searchQuery != null) {
                $resultsUsers = $em->getRepository(User::class)->createQueryBuilder('o')
                                                                ->where('o.username LIKE :query')
                                                                ->orderBy('o.id', 'DESC')
                                                                ->setParameter('query', '%'.$searchQuery.'%')
                                                                ->getQuery()
                                                                ->getResult();

                $resultsSongs = $em->getRepository(Song::class)->createQueryBuilder('o');

                $resultsSongs->where('o.title LIKE :query');
                $resultsSongs->orWhere('o.subtitle LIKE :query');
                $resultsSongs->orWhere('o.tags LIKE :query');
                $resultsSongs->orWhere('o.artist LIKE :query');
                $resultsSongs->orWhere('o.charter LIKE :query');

                // Add Filters for difficulty
                $filterEasy = $request->query->get('diffEasy') == 'on' ? true : false;
                $filterNormal = $request->query->get('diffNormal') == 'on' ? true : false;
                $filterHard = $request->query->get('diffHard') == 'on' ? true : false;
                $filterExpert = $request->query->get('diffExpert') == 'on' ? true : false;
                $filterXD = $request->query->get('diffXD') == 'on' ? true : false;

                // Add Filters for difficulty ratings
                $filterMinDifficulty = intval($request->query->get('diffRatingFrom'));
                $filterMaxDifficulty = intval($request->query->get('diffRatingTo'));

                if($filterMinDifficulty == null) { $filterMinDifficulty = 0; }
                if($filterMaxDifficulty == null) { $filterMaxDifficulty = 99; }

                $resultsSongs->setParameter('query', "%".$searchQuery."%");
                $resultsSongs->andWhere('o.publicationStatus IN (0, 1)');
                $resultsSongs->orderBy('o.id', 'DESC');

                $resultsSongs = $resultsSongs->getQuery()->getResult();

                // Filter Song Results
                $filteredResultsSongs = [];
                foreach($resultsSongs as $resultSong) {
                    // Has the required difficulty
                    if($filterEasy && $resultSong->getHasEasyDifficulty() ||
                        $filterNormal && $resultSong->getHasNormalDifficulty() ||
                        $filterHard && $resultSong->getHasHardDifficulty() ||
                        $filterExpert && $resultSong->getHasExtremeDifficulty() ||
                        $filterXD && $resultSong->getHasXDDifficulty()) {

                        // Has the minimum difficulty rating
                        if($resultSong->getHasEasyDifficulty() && $resultSong->getEasyDifficulty() >= $filterMinDifficulty ||
                            $resultSong->getHasNormalDifficulty() && $resultSong->getNormalDifficulty() >= $filterMinDifficulty ||
                            $resultSong->getHasHardDifficulty() && $resultSong->getHardDifficulty() >= $filterMinDifficulty ||
                            $resultSong->getHasExtremeDifficulty() && $resultSong->getExpertDifficulty() >= $filterMinDifficulty ||
                            $resultSong->getHasXDDifficulty() && $resultSong->getXDDifficulty() >= $filterMinDifficulty) {

                            // Has the maximum difficulty rating
                            if($resultSong->getHasEasyDifficulty() && $resultSong->getEasyDifficulty() <= $filterMaxDifficulty ||
                                $resultSong->getHasNormalDifficulty() && $resultSong->getNormalDifficulty() <= $filterMaxDifficulty ||
                                $resultSong->getHasHardDifficulty() && $resultSong->getHardDifficulty() <= $filterMaxDifficulty ||
                                $resultSong->getHasExtremeDifficulty() && $resultSong->getExpertDifficulty() <= $filterMaxDifficulty ||
                                $resultSong->getHasXDDifficulty() && $resultSong->getXDDifficulty() <= $filterMaxDifficulty) {

                                $filteredResultsSongs[] = $resultSong;
                            }
                        }
                    }
                }

                $data['results']['users'] = $resultsUsers;
                $data['results']['songs'] = $filteredResultsSongs;
            }
        }

        $data['searchQuery'] = $searchQuery;
        $data['filterEasy'] = $filterEasy;
        $data['filterNormal'] = $filterNormal;
        $data['filterHard'] = $filterHard;
        $data['filterExpert'] = $filterExpert;
        $data['filterXD'] = $filterXD;

        $data['diffRatingFrom'] = $filterMinDifficulty;
        $data['diffRatingTo'] = $filterMaxDifficulty;

        return $this->render('search/index.html.twig', $data);
    }
}
