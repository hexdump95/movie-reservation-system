<?php

namespace App\DTO;

class AddShowtimeRequest
{
    private \DateTime $dateStart;
    private int $theaterId;

    public function getDateStart(): \DateTime
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTime $dateStart): static
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    public function getTheaterId(): int
    {
        return $this->theaterId;
    }

    public function setTheaterId($theaterId): static
    {
        $this->theaterId = $theaterId;
        return $this;
    }
}
