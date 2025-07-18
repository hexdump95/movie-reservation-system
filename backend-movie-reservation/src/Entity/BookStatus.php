<?php

namespace App\Entity;

use App\Repository\BookStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookStatusRepository::class)]
#[ORM\HasLifecycleCallbacks]
class BookStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $name = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deletedAt = null;

    /**
     * @var Collection<int, StatusBook>
     */
    #[ORM\OneToMany(targetEntity: StatusBook::class, mappedBy: 'bookStatus')]
    private Collection $statusBooks;

    public function __construct()
    {
        $this->statusBooks = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

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
     * @return Collection<int, StatusBook>
     */
    public function getStatusBooks(): Collection
    {
        return $this->statusBooks;
    }

    public function addStatusBook(StatusBook $statusBook): static
    {
        if (!$this->statusBooks->contains($statusBook)) {
            $this->statusBooks->add($statusBook);
            $statusBook->setBookStatus($this);
        }

        return $this;
    }

    public function removeStatusBook(StatusBook $statusBook): static
    {
        if ($this->statusBooks->removeElement($statusBook)) {
            // set the owning side to null (unless already changed)
            if ($statusBook->getBookStatus() === $this) {
                $statusBook->setBookStatus(null);
            }
        }

        return $this;
    }
}
