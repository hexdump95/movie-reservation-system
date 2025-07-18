<?php

namespace App\Entity;

use App\Repository\StatusBookRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatusBookRepository::class)]
#[ORM\HasLifecycleCallbacks]
class StatusBook
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $dateFrom = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateTo = null;

    #[ORM\ManyToOne(inversedBy: 'statusBook')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\ManyToOne(inversedBy: 'statusBooks')]
    #[ORM\JoinColumn(nullable: false)]
    private ?BookStatus $bookStatus = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->dateFrom = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFrom(): ?\DateTimeInterface
    {
        return $this->dateFrom;
    }

    public function setDateFrom(\DateTimeInterface $dateFrom): static
    {
        $this->dateFrom = $dateFrom;

        return $this;
    }

    public function getDateTo(): ?\DateTimeInterface
    {
        return $this->dateTo;
    }

    public function setDateTo(?\DateTimeInterface $dateTo): static
    {
        $this->dateTo = $dateTo;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }

    public function getBookStatus(): ?BookStatus
    {
        return $this->bookStatus;
    }

    public function setBookStatus(?BookStatus $bookStatus): static
    {
        $this->bookStatus = $bookStatus;

        return $this;
    }
}
