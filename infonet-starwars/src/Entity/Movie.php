<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    /**
     * @var Collection<int, MovieCharacter>
     */
    #[ORM\OneToMany(targetEntity: MovieCharacter::class, mappedBy: 'movie')]
    private Collection $character;

    public function __construct()
    {
        $this->character = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, MovieCharacter>
     */
    public function getCharacter(): Collection
    {
        return $this->character;
    }

    public function addCharacter(MovieCharacter $character): static
    {
        if (!$this->character->contains($character)) {
            $this->character->add($character);
            $character->setMovie($this);
        }

        return $this;
    }

    public function removeCharacter(MovieCharacter $character): static
    {
        if ($this->character->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getMovie() === $this) {
                $character->setMovie(null);
            }
        }

        return $this;
    }
}
