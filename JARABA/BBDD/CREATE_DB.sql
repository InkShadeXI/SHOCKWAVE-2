CREATE DATABASE IF NOT EXISTS RedSocial;

USE RedSocial;

CREATE TABLE Usuario (
    IdUsuario INT AUTO_INCREMENT PRIMARY KEY,
    NombreUsuario VARCHAR(30) NOT NULL,
    CorreoUsuario VARCHAR(30) NOT NULL,
    ContrasenaUsuario VARCHAR(100) NOT NULL,
    UsuarioAdmin BOOLEAN NOT NULL,
    Localidad VARCHAR(100),
    Descripcion VARCHAR(255),
    FechaNacimiento DATE
);

CREATE TABLE Amistad (
    IdAmistad INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuario1 INT,
    IdUsuario2 INT,
    Estado VARCHAR(100), -- Cambiado a VARCHAR
    FOREIGN KEY (IdUsuario1) REFERENCES Usuario(IdUsuario) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (IdUsuario2) REFERENCES Usuario(IdUsuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE PostUsuario (
    IdPost INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuarioPost INT NOT NULL,
    TextoPost VARCHAR(300) NOT NULL,
    ExisteFoto BOOLEAN NOT NULL CHECK (ExisteFoto = FALSE OR TituloFoto IS NOT NULL), -- Corregida la restricción
    TituloFoto VARCHAR(30),
    NumLikes INT,
    NumDislikes INT,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (IdUsuarioPost) REFERENCES Usuario(IdUsuario) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Comentario (
    IdComentario INT AUTO_INCREMENT PRIMARY KEY,
    IdComentarioPost INT,
    IdComentarioUsuario INT,
    TextoComentario VARCHAR(300),
    FOREIGN KEY (IdComentarioPost) REFERENCES PostUsuario(IdPost) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (IdComentarioUsuario) REFERENCES Usuario(IdUsuario) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO `usuario` (`IdUsuario`, `NombreUsuario`, `CorreoUsuario`, `ContrasenaUsuario`, `UsuarioAdmin`, `Localidad`, `Descripcion`, `FechaNacimiento`) VALUES
(1, 'Jaraba', 'jaraba@gmail.com', '1234', 1, NULL, NULL, NULL),
(2, 'usuario1', 'sus1@gmail.com', '1234', 0, NULL, NULL, NULL),
(3, 'usuario2', 'usu2@gmail.com', '1234', 0, NULL, NULL, NULL),
(4, 'usuario3', 'sus3@gmail.com', '1234', 0, NULL, NULL, NULL),
(5, 'usuario4', 'usu4@gmail.com', '1234', 0, NULL, NULL, NULL),
(6, 'juan', 'hola1234@gmail.com', '12345678', 0, 'Tielmes Country', 'hola muy buenas a todos', '2004-06-03'),
(8, 'manolo', 'manolo@gmail.com', '12345678', 0, 'Toledo', 'De toledo', '1986-05-21'),
(9, 'fran', 'fran@gmail.com', '$2y$10$Vp.0yJUE.L4WmTnOgvpsr.TtbLHm6TlKBxf32aVgLzzxqUCD6hn7O', 1, 'Getafe', 'Dando guerra desde 2008', '2008-07-13'),
(36, 'Trump', 'trump@gmail.com', '$2y$10$Vp.0yJUE.L4WmTnOgvpsr.TtbLHm6TlKBxf32aVgLzzxqUCD6hn7O', 0, 'LOS GRANDES ESTADOS UNIDOS', 'Make America Great Again', '1946-06-14'),
(38, 'ald', 'ald@gmail.com', '$2y$10$1irr1ZEDQqM4n4xUZ8ttq.GV28uIC7phh4LGzXndIk/0buj6f5Fti', 0, 'narnia', 'a', '2004-11-13');

INSERT INTO `amistad` (`IdAmistad`, `IdUsuario1`, `IdUsuario2`, `Estado`) VALUES
(1, 2, 1, 'aceptada'),
(11, 4, 3, 'pendiente'),
(12, 2, 4, 'aceptada'),
(13, 1, 4, 'pendiente'),
(15, 5, 4, 'pendiente'),
(16, 9, 3, 'pendiente'),
(17, 9, 1, 'pendiente'),
(18, 1, NULL, 'pendiente'),
(20, 38, 9, 'aceptada');

INSERT INTO PostUsuario (IdUsuarioPost, TextoPost, ExisteFoto, TituloFoto, NumLikes, NumDislikes, FechaCreacion) VALUES
(1, '¡Hola mundo!', 0, NULL, 10, 2, '2024-02-29'),
(2, '¡Este es otro post!', 1, 'Imagen1.jpg', 5, 1, '2024-02-28');

-- Insertar algunos comentarios
INSERT INTO Comentario (IdComentarioPost, IdComentarioUsuario, TextoComentario) VALUES
(1, 2, '¡Qué buen post!'),
(1, 3, 'Interesante'),
(2, 1, 'Me gusta la imagen'),
(2, 4, '¿Dónde fue tomada la foto?');

DELIMITER //
CREATE TRIGGER before_insert_Usuario
BEFORE INSERT ON Usuario
FOR EACH ROW
BEGIN
    IF NEW.FechaNacimiento >= CURDATE() THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'La Fecha de Nacimiento debe ser anterior a la fecha actual';
    END IF;
END;
//
DELIMITER ;
