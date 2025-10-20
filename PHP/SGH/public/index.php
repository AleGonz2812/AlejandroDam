<?php
header('Access-Control-Allow-Origin: *');

// --- Manual class loading ---
require_once __DIR__ . '/../src/Config/Database.php';
require_once __DIR__ . '/../src/Service/ReservationService.php';

// --- .env loader ---
function loadEnv($file) {
    if (!file_exists($file)) return;
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') continue;
        $parts = explode('=', $line, 2);
        if (count($parts) === 2) {
            putenv("{$parts[0]}={$parts[1]}");
        }
    }
}
loadEnv(__DIR__ . '/../.env');

error_log('DB_HOST=' . getenv('DB_HOST'));
error_log('DB_NAME=' . getenv('DB_NAME'));
error_log('DB_USER=' . getenv('DB_USER'));
error_log('DB_PASS=' . getenv('DB_PASS'));

// --- Helper: JSON response ---
function jsonResponse($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Normalizar rutas si vienen con /index.php/...
$uri = preg_replace('#^/index\.php#', '', $uri);
if ($uri === '') $uri = '/';

try {
    $pdo = Src\Config\Database::getConnection();
    
$stmt = $pdo->query("SELECT DATABASE() AS db");
$dbName = $stmt->fetch()['db'];
error_log('Conectado a la base de datos: ' . $dbName);
} catch (Exception $e) {
    http_response_code(500);
    echo "Error DB: " . $e->getMessage();
    exit;
}
if ($method === 'GET' && $uri === '/guests') {
    $stmt = $pdo->query("SELECT id, name, email, documento_identidad, created_at FROM guests ORDER BY id DESC");
    header('Content-Type: application/json');
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}
if ($method === 'POST' && $uri === '/guests') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    foreach (['name','email'] as $f) if (empty($input[$f])) jsonResponse(['success'=>false,'error'=>"Falta campo: $f"], 400);
    try {
        $ins = $pdo->prepare("INSERT INTO guests (name, email, documento_identidad) VALUES (?, ?, ?)");
        $ins->execute([$input['name'], $input['email'], $input['documento_identidad'] ?? null]);
        jsonResponse(['success'=>true, 'guest_id' => (int)$pdo->lastInsertId()], 201);
    } catch (Exception $e) {
        jsonResponse(['success'=>false,'error'=>$e->getMessage()], 400);
    }
}
if ($method === 'GET' && $uri === '/rooms') {
    $stmt = $pdo->query("SELECT id, number, type, price_base, cleaning_state, created_at FROM rooms ORDER BY id");
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}
if ($method === 'POST' && $uri === '/rooms') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    foreach (['number','type','price_base'] as $f) if (!isset($input[$f])) jsonResponse(['success'=>false,'error'=>"Falta campo: $f"], 400);
    try {
        $ins = $pdo->prepare("INSERT INTO rooms (number, type, price_base, cleaning_state) VALUES (?, ?, ?, ?)");
        $ins->execute([$input['number'], $input['type'], (float)$input['price_base'], $input['cleaning_state'] ?? 'Sucia']);
        jsonResponse(['success'=>true, 'room_id' => (int)$pdo->lastInsertId()], 201);
    } catch (Exception $e) {
        jsonResponse(['success'=>false,'error'=>$e->getMessage()], 400);
    }
}
if ($method === 'GET' && $uri === '/maintenance') {
    $stmt = $pdo->query("SELECT id, room_id, descripcion, fecha_inicio, fecha_fin_expected, activo, created_at FROM maintenance_tasks ORDER BY id DESC");
    jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}
if ($method === 'POST' && $uri === '/maintenance') {
    $input = json_decode(file_get_contents('php://input'), true) ?: [];
    foreach (['room_id','fecha_inicio','fecha_fin_expected','descripcion'] as $f) if (!isset($input[$f])) jsonResponse(['success'=>false,'error'=>"Falta campo: $f"], 400);
    try {
        $ins = $pdo->prepare("INSERT INTO maintenance_tasks (room_id, descripcion, fecha_inicio, fecha_fin_expected, activo) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([(int)$input['room_id'], $input['descripcion'], $input['fecha_inicio'], $input['fecha_fin_expected'], isset($input['activo']) ? (int)$input['activo'] : 1]);
        jsonResponse(['success'=>true, 'maintenance_id' => (int)$pdo->lastInsertId()], 201);
    } catch (Exception $e) {
        jsonResponse(['success'=>false,'error'=>$e->getMessage()], 400);
    }
}
if ($method === 'POST' && $uri === '/reserve') {
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    try {
        foreach (['guest_id', 'room_id', 'fecha_llegada', 'fecha_salida'] as $f) {
            if (empty($data[$f]) && $data[$f] !== '0') throw new Exception("Falta campo: $f");
        }
        $reservationId = Src\Service\ReservationService::createReservation(
            $pdo,
            (int)$data['guest_id'],
            (int)$data['room_id'],
            $data['fecha_llegada'],
            $data['fecha_salida'],
            $data['estado'] ?? 'Pendiente'
        );
        jsonResponse(['success' => true, 'reservation_id' => $reservationId], 201);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

// --- RESERVATIONS: listar, confirmar, cancelar ---
if ($method === 'GET' && $uri === '/reservations') {
        $sql = "
            SELECT r.*, g.name AS guest_name, ro.number AS room_number
            FROM reservations r
            JOIN guests g ON g.id = r.guest_id
            JOIN rooms ro ON ro.id = r.room_id
            ORDER BY r.id DESC
        ";
        $stmt = $pdo->query($sql);
        jsonResponse(['success' => true, 'data' => $stmt->fetchAll()]);
}

// POST /reservations/{id}/confirm
if ($method === 'POST' && preg_match('#^/reservations/(\d+)/confirm$#', $uri, $m)) {
    $resId = (int)$m[1];
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$resId]);
        $res = $stmt->fetch();
        if (!$res) throw new Exception("Reserva no encontrada");
        if ($res['estado'] === 'Confirmada') throw new Exception("Ya está confirmada");

        $roomId = (int)$res['room_id'];
        $fechaLlegada = $res['fecha_llegada'];
        $fechaSalida = $res['fecha_salida'];

        // comprobar solapamiento con otras reservas confirmadas
        $sqlOverlap = "
            SELECT COUNT(*) FROM reservations r
            WHERE r.room_id = ? AND r.estado = 'Confirmada' AND r.id != ?
              AND NOT (r.fecha_salida <= ? OR r.fecha_llegada >= ?)
        ";
        $stmt = $pdo->prepare($sqlOverlap);
        $stmt->execute([$roomId, $resId, $fechaLlegada, $fechaSalida]);
        if ((int)$stmt->fetchColumn() > 0) throw new Exception("Conflicto: otra reserva confirmada solapa esas fechas");

        // comprobar mantenimiento activo que se solape
        $sqlMaint = "
            SELECT COUNT(*) FROM maintenance_tasks m
            WHERE m.room_id = ? AND m.activo = 1
              AND NOT (m.fecha_fin_expected < ? OR m.fecha_inicio > ?)
        ";
        $stmt = $pdo->prepare($sqlMaint);
        $stmt->execute([$roomId, $fechaLlegada, $fechaSalida]);
        if ((int)$stmt->fetchColumn() > 0) throw new Exception("Conflicto: tarea de mantenimiento activa en esas fechas");

        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE reservations SET estado = 'Confirmada' WHERE id = ?");
        $upd->execute([$resId]);
        $pdo->commit();

        jsonResponse(['success' => true, 'reservation_id' => $resId, 'estado' => 'Confirmada'], 200);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

// POST /reservations/{id}/cancel
if ($method === 'POST' && preg_match('#^/reservations/(\d+)/cancel$#', $uri, $m)) {
    $resId = (int)$m[1];
    try {
        $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
        $stmt->execute([$resId]);
        $res = $stmt->fetch();
        if (!$res) throw new Exception("Reserva no encontrada");
        if ($res['estado'] === 'Cancelada') throw new Exception("Ya está cancelada");

        $pdo->beginTransaction();
        $upd = $pdo->prepare("UPDATE reservations SET estado = 'Cancelada' WHERE id = ?");
        $upd->execute([$resId]);
        $pdo->commit();

        jsonResponse(['success' => true, 'reservation_id' => $resId, 'estado' => 'Cancelada'], 200);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        jsonResponse(['success' => false, 'error' => $e->getMessage()], 400);
    }
}

http_response_code(404);
echo json_encode(['success' => false, 'error' => 'Not Found']);