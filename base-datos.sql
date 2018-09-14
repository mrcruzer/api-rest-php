/* Esto es para crear una base de datos en Mysql */

CREATE DATABASE IF NOT EXISTS angular_backend;
USE angular_backend;

CREATE TABLE productos(
id int(255) auto_increment not null,
nombre varchar(255),
description text,
precio varchar(255),
imagen varchar(255),
CONSTRAINT pk_productos PRIMARY KEY(id)
)ENGINE=InnoDb;