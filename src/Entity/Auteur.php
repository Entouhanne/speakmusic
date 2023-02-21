<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AuteurRepository::class)]
class Auteur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'idAuteur', targetEntity: Musique::class)]
    private Collection $musiques;

    #[ORM\Column(length: 500)]
    private ?string $Illustration = null;

    #[ORM\ManyToOne(inversedBy: 'auteurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Genre $idGenre = null;

    #[ORM\ManyToOne(inversedBy: 'auteurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Pays $pays = null;

    #[ORM\Column]
    private ?int $dateFormation = null;

    #[ORM\ManyToOne(inversedBy: 'auteurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TypeAuteur $type = null;

    #[ORM\OneToMany(mappedBy: 'idAuteur', targetEntity: Album::class)]
    private Collection $albums;

    public function __construct()
    {
        $this->musiques = new ArrayCollection();
        $this->albums = new ArrayCollection();
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

    /**
     * @return Collection<int, Musique>
     */
    public function getMusiques(): Collection
    {
        return $this->musiques;
    }

    public function addMusique(Musique $musique): self
    {
        if (!$this->musiques->contains($musique)) {
            $this->musiques->add($musique);
            $musique->setIdAuteur($this);
        }

        return $this;
    }

    public function removeMusique(Musique $musique): self
    {
        if ($this->musiques->removeElement($musique)) {
            // set the owning side to null (unless already changed)
            if ($musique->getIdAuteur() === $this) {
                $musique->setIdAuteur(null);
            }
        }

        return $this;
    }

    public function getIllustration(): ?string
    {
        return $this->Illustration;
    }

    public function setIllustration(string $Illustration): self
    {
        $this->Illustration = $Illustration;

        return $this;
    }

    public function getIdGenre(): ?Genre
    {
        return $this->idGenre;
    }

    public function setIdGenre(?Genre $idGenre): self
    {
        $this->idGenre = $idGenre;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getDateFormation(): ?int
    {
        return $this->dateFormation;
    }

    public function setDateFormation(int $dateFormation): self
    {
        $this->dateFormation = $dateFormation;

        return $this;
    }

    public function getType(): ?TypeAuteur
    {
        return $this->type;
    }

    public function setType(?TypeAuteur $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection<int, Album>
     */
    public function getAlbums(): Collection
    {
        return $this->albums;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->albums->contains($album)) {
            $this->albums->add($album);
            $album->setIdAuteur($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->albums->removeElement($album)) {
            // set the owning side to null (unless already changed)
            if ($album->getIdAuteur() === $this) {
                $album->setIdAuteur(null);
            }
        }

        return $this;
    }
}
