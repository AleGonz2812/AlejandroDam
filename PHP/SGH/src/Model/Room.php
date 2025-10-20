<?php

namespace Src\Model;

class Room
{
    public ?int $id = null;
    public string $number;
    public string $type;
    public float $price_base;
    public string $cleaning_state;

    public function __construct(string $number, string $type, float $price_base, string $cleaning_state = 'Sucia', ?int $id = null)
    {
        $this->number = $number;
        $this->type = $type;
        $this->price_base = $price_base;
        $this->cleaning_state = $cleaning_state;
        $this->id = $id;
    }
}