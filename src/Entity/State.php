<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\StateRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class State
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    public const STATE_MEDIUM    = "Etat moyen";
    public const STATE_NORMAL    = "Bon état";
    public const STATE_GOOD      = "Très bon état";
    public const STATE_VERY_GOOD = "Comme neuf";
    public const STATE_NEW       = "Neuf";

    public static $states = [
        self::STATE_MEDIUM => "Etat moyen",
        self::STATE_NORMAL => "Bon état",
        self::STATE_GOOD => "Très bon état",
        self::STATE_VERY_GOOD => "Comme neuf",
        self::STATE_NEW => "Neuf",
    ];

    public static $statesOrder = [
        self::STATE_MEDIUM => 1,
        self::STATE_NORMAL => 2,
        self::STATE_GOOD => 3,
        self::STATE_VERY_GOOD => 4,
        self::STATE_NEW => 5,
    ];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $currentState = self::STATE_GOOD;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private int $priority = 3;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="state")
     */
    private $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCurrentState()
    {
        return $this->currentState;
    }

    public function setCurrentState(string $state)
    {
        $this->currentState = $state;

        return $this;
    }

    public function getPriority() : int
    {
        return $this->priority;
    }

    /**
     * @ORM\PrePersist()
     */
    private function setPriority(int $priority)
    {
        if (!\in_array($priority, self::$statesOrder)) {
            throw new \Exception("Invalid state ! Cannot provide a valid priority");
        }

        //$this->priority = self::$statesOrder[$this->getCurrentState()];
        $this->priority = $priority;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setState($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getState() === $this) {
                $game->setState(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->currentState;
    }
}
