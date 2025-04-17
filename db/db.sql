DROP USER IF EXISTS `login-system`@'%';
CREATE USER `login-system`@'%' IDENTIFIED BY 'sup3rs3cr3t';

DROP DATABASE IF EXISTS `login-system`;
CREATE DATABASE `login-system`;

USE `login-system`;

GRANT SELECT, INSERT, UPDATE, DELETE ON `login-system`.* TO `login-system`@'%';

DROP TABLE IF EXISTS users;

CREATE TABLE users(
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    gender ENUM('male', 'famale'),
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(60) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO users(id, name, surname, gender, phone, email, password)
VALUES(10000001, "Adão", "Major", "male", "+244939374984", "adaomajor01@gmail.com", "25d55ad283aa400af464c76d713c07ad");