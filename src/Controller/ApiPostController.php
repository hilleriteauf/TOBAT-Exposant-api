<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CoordonneeRepository;
use App\Repository\ExposantRepository;
use App\Repository\AdminRepository;
use App\Repository\StandRepository;
use App\Entity\Stand;
use App\Entity\Coordonnee;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Request;


class ApiPostController extends AbstractController
{

    /**
     * @Route("/api/get_exposant", name="api_get_exposant", methods={"POST"})
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
     * @Route("/api/authenticate", name="api_authenticate", methods={"POST"})
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

    /**
     * @Route("/api/delete_stand", name="api_delete_stand", methods={"POST"})
     */
    public function deleteStand(Request $request, StandRepository $standRepository, CoordonneeRepository $coordonneesRepository)
    {
        $idStand = $postData = json_decode($request->getContent(), true)["id_stand"];

        $entityManager = $this->getDoctrine()->getManager();
        
        $coordonnees = $coordonneesRepository->findBy(["idStand" => $idStand]);
        foreach ($coordonnees as $coord) {
            $entityManager->remove($coord);
        }
        $entityManager->flush();

        $stand = $standRepository->find($idStand);
        if ($stand != null){
            $entityManager->remove($stand);
            $entityManager->flush();
        }

        $jsonReponse = json_encode(["status" => "success"]);

        $response = new JsonResponse($jsonReponse, 200, [], true);

        return $response;
    }

    /**
     * @Route("/api/create_stand", name="api_create_stand", methods={"POST"})
     */
    public function createStand(Request $request, StandRepository $standRepository, CoordonneeRepository $coordonneesRepository)
    {
        $postData = json_decode($request->getContent(), true);

        $entityManager = $this->getDoctrine()->getManager();
        
        $standName = $postData["nom_stand"];
        $coordonnees = $postData["coordonnees"];
        
        $alreadyExists = false;
        $outOfBounds = false;

        if ($coordonnees["nord"] < 0 || $coordonnees["nord"] > 4) $outOfBounds = true;
        if ($coordonnees["sud"] < 0 || $coordonnees["sud"] > 4) $outOfBounds = true;
        if ($coordonnees["est"] < 0 || $coordonnees["est"] > 9) $outOfBounds = true;
        if ($coordonnees["ouest"] < 0 || $coordonnees["ouest"] > 9) $outOfBounds = true;

        for ($y=$coordonnees["nord"]; $y <= $coordonnees["sud"] ; $y++) { 
            for ($x=$coordonnees["ouest"]; $x <= $coordonnees["est"]; $x++) { 
                if ($coordonneesRepository->findBy(["x" => $x, "y" => $y]) != null)
                {
                    $alreadyExists = true;
                }
            }
        }

        if ($alreadyExists || $outOfBounds)
        {
            $jsonReponse = json_encode(["status" => "fail"]);
            return new JsonResponse($jsonReponse, 200, [], true);
        }

        $stand = new Stand();
        $stand->setIdStand($standRepository->getMaxId() +1);
        $stand->setNom($standName);
        $entityManager->persist($stand);
        $entityManager->flush();

        for ($y=$coordonnees["nord"]; $y <= $coordonnees["sud"] ; $y++) { 
            for ($x=$coordonnees["ouest"]; $x <= $coordonnees["est"]; $x++) { 
                $coordonnee = new Coordonnee();
                $coordonnee->setX($x);
                $coordonnee->setY($y);
                $coordonnee->setIdStand($stand->getIdStand());
                $entityManager->persist($coordonnee);
            }
        }

        $entityManager->flush();

        $jsonReponse = json_encode(["status" => "success"]);

        return new JsonResponse($jsonReponse, 200, [], true);
    }
}
