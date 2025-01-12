<?php

namespace App\Entity;

use App\Repository\SeatRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SeatRepository::class)]
class Seat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $column_ = null;

    #[ORM\Column]
    private ?int $row_ = null;

    #[ORM\Column(length: 10)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'seats')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Theater $theater = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getColumn(): ?int
    {
        return $this->column_;
    }

    public function setColumn(int $column_): static
    {
        $this->column_ = $column_;

        return $this;
    }

    public function getRow(): ?int
    {
        return $this->row_;
    }

    public function setRow(int $row_): static
    {
        $this->row_ = $row_;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getTheater(): ?Theater
    {
        return $this->theater;
    }

    public function setTheater(?Theater $theater): static
    {
        $this->theater = $theater;

        return $this;
    }

}
