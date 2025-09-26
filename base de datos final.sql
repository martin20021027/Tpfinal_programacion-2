CREATE TABLE `cliente` (
  `ID_cliente` int NOT NULL AUTO_INCREMENT,
  `nombre_apellido` varchar(50) NOT NULL,
  `numero_reserva` int NOT NULL,
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



