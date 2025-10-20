<?php

namespace Src\Service;

use PDO;
use Exception;
use DateTime;

class ReservationService
{
    /**
     * Crea una reserva aplicando validaciones:
     * - fechas válidas (llegada < salida)
     * - huésped y habitación existen
     * - no solapamiento con reservas Confirmadas
     * - no conflicto con tareas de mantenimiento activas
     * - calcula precio_total = noches * price_base
     * Devuelve id de la reserva creada o lanza Exception.
     */
    public static function createReservation(PDO $pdo, int $guestId, int $roomId, string $fechaLlegada, string $fechaSalida, string $estado = 'Pendiente'): int
    {
        $d1 = new DateTime($fechaLlegada);
        $d2 = new DateTime($fechaSalida);
        if ($d1 >= $d2) {
            throw new Exception("fecha_llegada debe ser anterior a fecha_salida");
        }

        // comprobar huésped
        $stmt = $pdo->prepare("SELECT id FROM guests WHERE id = ?");
        $stmt->execute([$guestId]);
        if (!$stmt->fetch()) {
            throw new Exception("Huésped no encontrado (id={$guestId})");
        }

        // obtener habitación y precio
        $stmt = $pdo->prepare("SELECT id, price_base FROM rooms WHERE id = ?");
        $stmt->execute([$roomId]);
        $room = $stmt->fetch();
        if (!$room) {
            throw new Exception("Habitación no encontrada (id={$roomId})");
        }

        // comprobar solapamiento con reservas Confirmadas
        $sqlOverlap = "
            SELECT COUNT(*) FROM reservations r
            WHERE r.room_id = ? AND r.estado = 'Confirmada'
              AND NOT (r.fecha_salida <= ? OR r.fecha_llegada >= ?)
        ";
        $stmt = $pdo->prepare($sqlOverlap);
        $stmt->execute([$roomId, $fechaLlegada, $fechaSalida]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("Habitación ocupada por una reserva confirmada en esas fechas");
        }

        // comprobar mantenimiento activo que se solape
        $sqlMaint = "
            SELECT COUNT(*) FROM maintenance_tasks m
            WHERE m.room_id = ? AND m.activo = 1
              AND NOT (m.fecha_fin_expected < ? OR m.fecha_inicio > ?)
        ";
        $stmt = $pdo->prepare($sqlMaint);
        $stmt->execute([$roomId, $fechaLlegada, $fechaSalida]);
        if ((int)$stmt->fetchColumn() > 0) {
            throw new Exception("Habitación con tarea de mantenimiento activa en esas fechas");
        }

        // calcular precio_total
        $nights = (int)$d2->diff($d1)->format('%a');
        if ($nights <= 0) {
            throw new Exception("Periodo inválido (noches <= 0)");
        }
        $precioTotal = $room['price_base'] * $nights;

        // insertar en transacción
        try {
            $pdo->beginTransaction();
            $ins = $pdo->prepare("
                INSERT INTO reservations (guest_id, room_id, fecha_llegada, fecha_salida, precio_total, estado)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $ins->execute([$guestId, $roomId, $fechaLlegada, $fechaSalida, $precioTotal, $estado]);
            $id = (int)$pdo->lastInsertId();
            $pdo->commit();
            return $id;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            throw $e;
        }
    }
}