<?php

namespace Src\Model;

class Guest
{
    public ?int $id = null;
    public string $name;
    public string $email;
    public ?string $documento_identidad;

    public function __construct(string $name, string $email, ?string $documento_identidad = null, ?int $id = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->documento_identidad = $documento_identidad;
        $this->id = $id;
    }
}