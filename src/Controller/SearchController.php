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
            $resultsSongs = $em->getRepository(Song::class)->findAll();

            $data['results']['users'] = $resultsUsers;
            $data['results']['songs'] = $resultsSongs;
        } else {
            if($searchQuery != null) {
                $resultsUsers = $em->getRepository(User::class)->createQueryBuilder('o')
                                                                ->where('o.username LIKE :query')
                                                                ->orderBy('o.id', 'DESC')
                                                                ->setParameter('query', '%'.$searchQuery.'%')
                                                                ->getQuery()
                                                                ->getResult();

                
                $resultsSongs = $em->getRepository(Song::class)->createQueryBuilder('o')
                                                                ->where('o.title LIKE :query')
                                                                ->orWhere('o.subtitle LIKE :query')
                                                                ->orWhere('o.tags LIKE :query')
                                                                ->orWhere('o.artist LIKE :query')
                                                                ->orWhere('o.charter LIKE :query')
                                                                ->orderBy('o.id', 'DESC')
                                                                ->setParameter('query', '%'.$searchQuery.'%')
                                                                ->getQuery()
                                                                ->getResult();

                $data['results']['users'] = $resultsUsers;
                $data['results']['songs'] = $resultsSongs;
            }
        }
        $data['searchQuery'] = $searchQuery;

        return $this->render('search/index.html.twig', $data);
    }
}
