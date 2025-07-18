<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?float $totalPrice = null;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_ = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'book', cascade: ['remove', 'persist', 'refresh', 'detach'], orphanRemoval: true)]
    private Collection $tickets;

    #[ORM\ManyToOne(inversedBy: 'books')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Showtime $showtime = null;

    /**
     * @var Collection<int, StatusBook>
     */
    #[ORM\OneToMany(targetEntity: StatusBook::class, mappedBy: 'book',  cascade: ['remove', 'persist', 'refresh', 'detach'], orphanRemoval: true)]
    private Collection $statusBook;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
        $this->statusBook = new ArrayCollection();
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function setTotalPrice(float $totalPrice): static
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user_;
    }

    public function setUser(?User $user_): static
    {
        $this->user_ = $user_;

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $this->totalPrice += $ticket->getPrice();
            $ticket->setBook($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getBook() === $this) {
                $this->totalPrice -= $ticket->getPrice();
                $ticket->setBook(null);
            }
        }

        return $this;
    }

    public function getShowtime(): ?Showtime
    {
        return $this->showtime;
    }

    public function setShowtime(?Showtime $showtime): static
    {
        $this->showtime = $showtime;

        return $this;
    }

    /**
     * @return Collection<int, StatusBook>
     */
    public function getStatusBook(): Collection
    {
        return $this->statusBook;
    }

    public function addStatusBook(StatusBook $statusBook): static
    {
        if (!$this->statusBook->contains($statusBook)) {
            $this->statusBook->add($statusBook);
            $statusBook->setBook($this);
        }

        return $this;
    }

    public function removeStatusBook(StatusBook $statusBook): static
    {
        if ($this->statusBook->removeElement($statusBook)) {
            // set the owning side to null (unless already changed)
            if ($statusBook->getBook() === $this) {
                $statusBook->setBook(null);
            }
        }

        return $this;
    }
}
