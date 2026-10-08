<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: \App\Entity\Evenement::class)]
    private \Doctrine\Common\Collections\Collection $evenements;

    public function __construct()
    {
        $this->evenements = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getEvenements(): \Doctrine\Common\Collections\Collection
    {
        return $this->evenements;
    }

    public function addEvenement(\App\Entity\Evenement $evenement): static
    {
        if (!$this->evenements->contains($evenement)) {
            $this->evenements->add($evenement);
            $evenement->setLieu($this);
        }
        return $this;
    }

    public function removeEvenement(\App\Entity\Evenement $evenement): static
    {
        if ($this->evenements->removeElement($evenement)) {
            if ($evenement->getLieu() === $this) {
                $evenement->setLieu(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }
}