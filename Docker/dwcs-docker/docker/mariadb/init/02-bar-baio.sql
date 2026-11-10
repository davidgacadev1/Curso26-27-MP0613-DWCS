-- Base de datos de ejemplo: la carta del Bar Baio
USE dwcs;

CREATE TABLE IF NOT EXISTS platos (
    id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(80)  NOT NULL,
    categoria ENUM('entrante', 'principal', 'postre') NOT NULL,
    precio    DECIMAL(6,2) NOT NULL,
    vegano    BOOLEAN      NOT NULL DEFAULT FALSE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO platos (nombre, categoria, precio, vegano) VALUES
    ('Pulpo á feira',        'principal', 16.50, FALSE),
    ('Pimientos de Padrón',  'entrante',   6.00, TRUE),
    ('Empanada de zamburiñas','entrante',  9.80, FALSE),
    ('Caldo gallego',        'entrante',   7.20, FALSE),
    ('Merluza á galega',     'principal', 18.90, FALSE),
    ('Tarta de Santiago',    'postre',     5.50, FALSE),
    ('Filloas con mel',      'postre',     4.80, FALSE);
