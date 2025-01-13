<?php

namespace App\Entity;

use App\Repository\TheaterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TheaterRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Theater
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $number = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, Seat>
     */
    #[ORM\OneToMany(targetEntity: Seat::class, mappedBy: 'theater', cascade: ['remove', 'persist', 'refresh', 'detach'], orphanRemoval: true)]
    #[ORM\OrderBy(['row_' => 'ASC', 'column_' => 'ASC'])]
    private Collection $seats;

    /**
     * @var Collection<int, Showtime>
     */
    #[ORM\OneToMany(targetEntity: Showtime::class, mappedBy: 'theater')]
    private Collection $showtimes;

    public function __construct()
    {
        $this->seats = new ArrayCollection();
        $this->showtimes = new ArrayCollection();
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

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        $this->number = $number;

        return $this;
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): static
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * @return Collection<int, Seat>
     */
    public function getSeats(): Collection
    {
        return $this->seats;
    }

    public function addSeat(Seat $seat): static
    {
        if (!$this->seats->contains($seat)) {
            $this->seats->add($seat);
            $seat->setTheater($this);
        }

        return $this;
    }

    public function removeSeat(Seat $seat): static
    {
        if ($this->seats->removeElement($seat)) {
            // set the owning side to null (unless already changed)
            if ($seat->getTheater() === $this) {
                $seat->setTheater(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Showtime>
     */
    public function getShowtimes(): Collection
    {
        return $this->showtimes;
    }

    public function addShowtime(Showtime $showtime): static
    {
        if (!$this->showtimes->contains($showtime)) {
            $this->showtimes->add($showtime);
            $showtime->setTheater($this);
        }

        return $this;
    }

    public function removeShowtime(Showtime $showtime): static
    {
        if ($this->showtimes->removeElement($showtime)) {
            // set the owning side to null (unless already changed)
            if ($showtime->getTheater() === $this) {
                $showtime->setTheater(null);
            }
        }

        return $this;
    }
}
