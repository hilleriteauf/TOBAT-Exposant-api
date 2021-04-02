<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoordonneeRepository;
use App\Repository\ExposantRepository;
use App\Repository\AdminRepository;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;


class ApiPostController extends AbstractController
{

    /**
     * @Route("/api/get_exposant/", name="api_get_exposant", methods={"POST"})
     */
    public function getExposant(Request $request, ExposantRepository $exposantRepository, NormalizerInterface $normalizer)
    {
        $postData = json_decode($request->getContent(), true);
        $idExposant = $postData["id"];

        $exposant = $exposantRepository->find($idExposant);

        $normalizedExposant = $normalizer->normalize($exposant, null);

        $jsonReponse = json_encode(array('exists' => ($exposant != null), 'exposant' => $normalizedExposant));

        $response = new JsonResponse($jsonReponse, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/authenticate/", name="api_authenticate", methods={"POST"})
     */
    public function authenticate(Request $request, AdminRepository $adminRepository, NormalizerInterface $normalizer)
    {
        $postData = json_decode($request->getContent(), true);
        $idAdmin = $postData["id_admin"];
        $mdp = $postData["mot_de_passe"];

        $admin = $adminRepository->findOneBy([
            'idAdmin' => $idAdmin,
            'motDePasse' => $mdp
        ]);

        $jsonReponse = json_encode(array('authenticationStatus' => ($admin == null ? "failed" : "success")));

        $response = new JsonResponse($jsonReponse, 200, [], true);

        return $response;
    }
}
