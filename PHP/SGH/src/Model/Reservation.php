<?php

namespace Src\Model;

class Reservation
{
    public ?int $id = null;
    public int $guest_id;
    public int $room_id;
    public string $fecha_llegada; // 'YYYY-MM-DD'
    public string $fecha_salida;  // 'YYYY-MM-DD'
    public float $precio_total;
    public string $estado; // Pendiente, Confirmada, Cancelada
    public ?string $fecha_reserva;

    public function __construct(int $guest_id, int $room_id, string $fecha_llegada, string $fecha_salida, float $precio_total, string $estado = 'Pendiente', ?int $id = null, ?string $fecha_reserva = null)
    {
        $this->guest_id = $guest_id;
        $this->room_id = $room_id;
        $this->fecha_llegada = $fecha_llegada;
        $this->fecha_salida = $fecha_salida;
        $this->precio_total = $precio_total;
        $this->estado = $estado;
        $this->id = $id;
        $this->fecha_reserva = $fecha_reserva;
    }
}