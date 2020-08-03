<?php

namespace App\Entity;

use App\Entity\State;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GameRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Game
{
    public const CENTIME = 0.01;

    public const EURO = 1;
    
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private float $price;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private float $floorPrice;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private float $maxPrice;

    /**
     * @ORM\ManyToOne(targetEntity=State::class, inversedBy="games", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private State $state;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFloorPrice(): float
    {
        return $this->floorPrice;
    }

    public function setFloorPrice(?float $floorPrice): self
    {
        $this->floorPrice = $floorPrice;

        return $this;
    }

    public function getMaxPrice(): float
    {
        return $this->maxPrice;
    }

    public function setMaxPrice(?float $maxPrice): self
    {
        $this->maxPrice = $maxPrice;

        return $this;
    }

    public function getState(): ?State
    {
        return $this->state;
    }

    public function setState(?State $state): self
    {
        $this->state = $state;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
