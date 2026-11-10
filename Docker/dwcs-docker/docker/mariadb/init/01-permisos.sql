-- =============================================================================
--  Se ejecuta SOLO la primera vez que arranca MariaDB (volumen de datos vacío).
--  Para repetirlo: docker compose down -v  (¡borra todas las bases de datos!)
-- =============================================================================
-- En desarrollo, el usuario "dwcs" puede crear y borrar cualquier base de datos
-- (como root en XAMPP). NUNCA hagas esto en un servidor real.
GRANT ALL PRIVILEGES ON *.* TO 'dwcs'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
