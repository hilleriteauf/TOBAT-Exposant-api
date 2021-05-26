<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoordonneeRepository;
use App\Repository\StandRepository;
use App\Repository\ExposantRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;


class ApiGetController extends AbstractController
{
    /**
     * @Route("/api/get_coordonnees", name="api_get_coordonnees", methods={"GET"})
     */
    public function getCoordonnees(CoordonneeRepository $coordonneesRepository, SerializerInterface $serializer)
    {
        $coordonnees = $coordonneesRepository->findAll();

        $json = $serializer->serialize($coordonnees, "json");

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/get_stands", name="api_get_stands", methods={"GET"})
     */
    public function getStands(StandRepository $standRepository, CoordonneeRepository $coordonneesRepository, ExposantRepository $exposantRepository, SerializerInterface $serializer)
    {
        $stands = $standRepository->findAll();

        $standsArray = array();
        foreach ($stands as $stand) {
            
            $codeExposant = $stand->getCodeExposant();
            if ($codeExposant != null)
            {
                $exposant = $exposantRepository->find($codeExposant);
            }
            else $exposant = null;
            $coordonnees = $coordonneesRepository->findBy(["idStand" => $stand->getIdStand()]);

            $standsArray[] = [
                "id_stand" => $stand->getIdStand(),
                "nom" => $stand->getNom(),
                "superficie" => count($coordonnees),
                "exposant" => $exposant,
                "coordonnees" => $coordonnees
            ];
        }
        $json = $serializer->serialize($standsArray, "json");

        $response = new JsonResponse($json, 200, [], true);

        return $response;
    }

}
