drop database if exists gestionHoteles;
CREATE DATABASE IF NOT EXISTS gestionHoteles;
USE gestionHoteles;

-- Rooms
CREATE TABLE IF NOT EXISTS rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  number VARCHAR(20) NOT NULL UNIQUE,
  type VARCHAR(50) NOT NULL,
  price_base DECIMAL(10,2) NOT NULL,
  cleaning_state ENUM('Limpia','Sucia','En Limpieza') NOT NULL DEFAULT 'Sucia',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Guests
CREATE TABLE IF NOT EXISTS guests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  documento_identidad VARCHAR(50),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Reservations
CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  guest_id INT NOT NULL,
  room_id INT NOT NULL,
  fecha_llegada DATE NOT NULL,
  fecha_salida DATE NOT NULL,
  precio_total DECIMAL(10,2) NOT NULL,
  estado ENUM('Pendiente','Confirmada','Cancelada') NOT NULL DEFAULT 'Pendiente',
  fecha_reserva DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_res_guest FOREIGN KEY (guest_id) REFERENCES guests(id) ON DELETE CASCADE,
  CONSTRAINT fk_res_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Index para consultas de solapamiento por habitación y rango de fechas
CREATE INDEX idx_res_room_dates ON reservations (room_id, fecha_llegada, fecha_salida);

-- Maintenance tasks
CREATE TABLE IF NOT EXISTS maintenance_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  descripcion TEXT,
  fecha_inicio DATE NOT NULL,
  fecha_fin_expected DATE NOT NULL,
  activo TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_maint_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE INDEX idx_maint_room_dates ON maintenance_tasks (room_id, fecha_inicio, fecha_fin_expected);

USE gestionHoteles;

INSERT INTO rooms (number, type, price_base, cleaning_state) VALUES
('101','Sencilla',50.00,'Limpia'),
('102','Doble',80.00,'Sucia'),
('201','Suite',150.00,'Limpia');

INSERT INTO guests (name, email, documento_identidad) VALUES
('Juan Pérez', 'juan@example.com', '12345678A'),
('María García', 'maria@example.com', '87654321B');

-- Ejemplo de tarea de mantenimiento (bloquea reservas que se solapen)
INSERT INTO maintenance_tasks (room_id, descripcion, fecha_inicio, fecha_fin_expected, activo) VALUES
(2, 'Arreglar grifo baño', '2025-11-05', '2025-11-07', 1);

