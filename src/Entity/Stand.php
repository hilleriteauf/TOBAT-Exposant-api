<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stand
 *
 * @ORM\Table(name="stand", indexes={@ORM\Index(name="code_exposant", columns={"code_exposant"})})
 * @ORM\Entity(repositoryClass="App\Repository\StandRepository")
 */
class Stand
{
    /**
     * @var int
     *
     * @ORM\Column(name="id_stand", type="integer", nullable=false)
     * @ORM\Id
     */
    private $idStand;
    //* @ORM\GeneratedValue(strategy="IDENTITY")

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom;

    /**
     * @var int|null
     * @ORM\OneToOne(targetEntity="App\Entity\Exposant", inversedBy="exposant")
     * @ORM\Column(name="code_exposant", type="integer", nullable=true)
     */
    private $codeExposant;

    public function getIdStand(): ?int
    {
        return $this->idStand;
    }

    public function setIdStand($idStand): self
    {
        $this->idStand = $idStand;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getCodeExposant(): ?int
    {
        return $this->codeExposant;
    }

    public function setCodeExposant(?int $codeExposant): self
    {
        $this->codeExposant = $codeExposant;

        return $this;
    }
}
