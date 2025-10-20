<?php

namespace Src\Model;

class MaintenanceTask
{
    public ?int $id = null;
    public int $room_id;
    public string $descripcion;
    public string $fecha_inicio;        // 'YYYY-MM-DD'
    public string $fecha_fin_expected;  // 'YYYY-MM-DD'
    public bool $activo;

    public function __construct(int $room_id, string $descripcion, string $fecha_inicio, string $fecha_fin_expected, bool $activo = true, ?int $id = null)
    {
        $this->room_id = $room_id;
        $this->descripcion = $descripcion;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin_expected = $fecha_fin_expected;
        $this->activo = $activo;
        $this->id = $id;
    }
}