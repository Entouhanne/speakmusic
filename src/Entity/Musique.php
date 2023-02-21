<?php

namespace App\Entity;

use App\Repository\MusiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusiqueRepository::class)]
class Musique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\ManyToOne(inversedBy: 'musiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Auteur $idAuteur = null;

    #[ORM\OneToMany(mappedBy: 'idMusique', targetEntity: Interpretation::class)]
    private Collection $interpretations;

    #[ORM\OneToMany(mappedBy: 'idMusique', targetEntity: SuivisMusique::class)]
    private Collection $suivisMusiques;

    #[ORM\Column(length: 3000, nullable: true)]
    private ?string $paroles = null;


    #[ORM\ManyToOne(inversedBy: 'musiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $idAlbum = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $lien = null;

    public function __construct()
    {
        $this->interpretations = new ArrayCollection();
        $this->suivisMusiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getIdAuteur(): ?Auteur
    {
        return $this->idAuteur;
    }

    public function setIdAuteur(?Auteur $idAuteur): self
    {
        $this->idAuteur = $idAuteur;

        return $this;
    }

    /**
     * @return Collection<int, Interpretation>
     */
    public function getInterpretations(): Collection
    {
        return $this->interpretations;
    }

    public function addInterpretation(Interpretation $interpretation): self
    {
        if (!$this->interpretations->contains($interpretation)) {
            $this->interpretations->add($interpretation);
            $interpretation->setIdMusique($this);
        }

        return $this;
    }

    public function removeInterpretation(Interpretation $interpretation): self
    {
        if ($this->interpretations->removeElement($interpretation)) {
            // set the owning side to null (unless already changed)
            if ($interpretation->getIdMusique() === $this) {
                $interpretation->setIdMusique(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SuivisMusique>
     */
    public function getSuivisMusiques(): Collection
    {
        return $this->suivisMusiques;
    }

    public function addSuivisMusique(SuivisMusique $suivisMusique): self
    {
        if (!$this->suivisMusiques->contains($suivisMusique)) {
            $this->suivisMusiques->add($suivisMusique);
            $suivisMusique->setIdMusique($this);
        }

        return $this;
    }

    public function removeSuivisMusique(SuivisMusique $suivisMusique): self
    {
        if ($this->suivisMusiques->removeElement($suivisMusique)) {
            // set the owning side to null (unless already changed)
            if ($suivisMusique->getIdMusique() === $this) {
                $suivisMusique->setIdMusique(null);
            }
        }

        return $this;
    }

    public function getParoles(): ?string
    {
        return $this->paroles;
    }

    public function setParoles(?string $paroles): self
    {
        $this->paroles = $paroles;

        return $this;
    }

    public function getIdAlbum(): ?Album
    {
        return $this->idAlbum;
    }

    public function setIdAlbum(?Album $idAlbum): self
    {
        $this->idAlbum = $idAlbum;

        return $this;
    }

    public function getLien(): ?string
    {
        return $this->lien;
    }

    public function setLien(?string $lien): self
    {
        $this->lien = $lien;

        return $this;
    }
}
