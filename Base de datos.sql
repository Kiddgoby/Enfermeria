CREATE DATABASE enfermeria;
USE enfermeria;

CREATE TABLE Paciente (
ID_Paciente INT PRIMARY KEY, 
Nombre VARCHAR(30),
Apellido VARCHAR (50),
Edad INT, 
Telefono INT,
Alergias VARCHAR (100), 
Altura DECIMAL(3,2),
Peso DECIMAL (5,2)
);