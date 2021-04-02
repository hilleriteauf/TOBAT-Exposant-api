<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoordonneeRepository;
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

}
