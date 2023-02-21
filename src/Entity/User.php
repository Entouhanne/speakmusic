<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: SuivisMusique::class)]
    private Collection $suivisMusiques;

    #[ORM\OneToMany(mappedBy: 'idUser', targetEntity: Interpretation::class)]
    private Collection $interpretations;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $ImageProfil = null;

    public function __construct()
    {
        $this->suivisMusiques = new ArrayCollection();
        $this->interpretations = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

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
            $suivisMusique->setIdUser($this);
        }

        return $this;
    }

    public function removeSuivisMusique(SuivisMusique $suivisMusique): self
    {
        if ($this->suivisMusiques->removeElement($suivisMusique)) {
            // set the owning side to null (unless already changed)
            if ($suivisMusique->getIdUser() === $this) {
                $suivisMusique->setIdUser(null);
            }
        }

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
            $interpretation->setIdUser($this);
        }

        return $this;
    }

    public function removeInterpretation(Interpretation $interpretation): self
    {
        if ($this->interpretations->removeElement($interpretation)) {
            // set the owning side to null (unless already changed)
            if ($interpretation->getIdUser() === $this) {
                $interpretation->setIdUser(null);
            }
        }

        return $this;
    }

    public function getImageProfil(): ?string
    {
        return $this->ImageProfil;
    }

    public function setImageProfil(?string $ImageProfil): self
    {
        $this->ImageProfil = $ImageProfil;

        return $this;
    }

}
