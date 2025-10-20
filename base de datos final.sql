CREATE TABLE `cliente` (
  `ID_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre_apellido` varchar(50) NOT NULL,
  `numero_reserva` int NOT NULL,
  `numero_mesa` int,
  PRIMARY KEY (`ID_cliente`)
);

CREATE TABLE `reserva` (
  `ID_reserva` int NOT NULL AUTO_INCREMENT,
  `estado_mesa` varchar(50) NOT NULL,
  `capacidad_mesa` int NOT NULL,
  `numero_mesa` int NOT NULL,
  `numero_reserva` int NOT NULL,
  PRIMARY KEY (`ID_reserva`) USING BTREE
);

CREATE TABLE  `usuarios` (
  `ID_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `pass` varchar(255) NOT NULL,
  PRIMARY KEY (`ID_usuario`)
) 

INSERT INTO reserva (estado_mesa, capacidad_mesa, numero_mesa, numero_reserva)
VALUES
('Disponible', 4, 1, 1001),
('Reservada', 2, 2, 1002),
('Disponible', 6, 3, 1003),
('Disponible', 4, 4, 1004),
('Reservada', 8, 5, 1005),
('Ocupada', 2, 6, 1006),
('Disponible', 4, 7, 1007);

INSERT INTO cliente (nombre_apellido, numero_reserva, numero_mesa)
VALUES
('Laura Gómez', 1001, 1),
('Carlos Pérez', 1002, 2),
('María López', 1003, 3),
('Andrés Rivas', 1004, 4),
('Sofía Márquez', 1005, 5),
('Daniel Ortega', 1006, 6),
('Lucía Torres', 1007, 7);

INSERT INTO usuarios (nombre, apellido, email, pass)
VALUES
('Ana', 'Martínez', 'ana.martinez@restaurante.com', 'hashed_pass_123'),
('Jorge', 'Serrano', 'jorge.serrano@restaurante.com', 'hashed_pass_456'),
('Elena', 'Cruz', 'elena.cruz@restaurante.com', 'hashed_pass_789'),
('Miguel', 'Ruiz', 'miguel.ruiz@restaurante.com', 'hashed_pass_abc'),
('Paula', 'Castro', 'paula.castro@restaurante.com', 'hashed_pass_def'),
('Ricardo', 'Vega', 'ricardo.vega@restaurante.com', 'hashed_pass_ghi'),
('Natalia', 'Suárez', 'natalia.suarez@restaurante.com', 'hashed_pass_jkl');






