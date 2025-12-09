-- Crear base de datos de testing si no existe
CREATE DATABASE IF NOT EXISTS mds_events_test;

-- Dar permisos completos al usuario mds_user
GRANT ALL PRIVILEGES ON mds_events_test.* TO 'mds_user'@'%';
FLUSH PRIVILEGES;
