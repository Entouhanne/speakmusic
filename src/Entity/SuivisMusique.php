<?php

namespace App\Entity;

use App\Repository\SuivisMusiqueRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SuivisMusiqueRepository::class)]
class SuivisMusique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'suivisMusiques')]
    private ?User $idUser = null;

    #[ORM\ManyToOne(inversedBy: 'suivisMusiques')]
    private ?Musique $idMusique = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?User
    {
        return $this->idUser;
    }

    public function setIdUser(?User $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    public function getIdMusique(): ?Musique
    {
        return $this->idMusique;
    }

    public function setIdMusique(?Musique $idMusique): self
    {
        $this->idMusique = $idMusique;

        return $this;
    }
}
