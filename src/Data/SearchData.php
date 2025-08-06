<?php

namespace App\Data;

use App\Entity\City;

class SearchData
{
    public ?City $departureCity = null;
    public ?City $arrivalCity = null;
    public ?\DateTimeInterface $date = null;
    public ?int $priceMax = null;
    public ?string $sort = null;
    public bool $eco = false;
    public bool $smoking = false;
    public bool $pets = false;
}