CREATE DATABASE RedSocial;

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
    IdUsuario1 INT,
    IdUsuario2 INT,
    FOREIGN KEY (IdUsuario1) REFERENCES Usuario(IdUsuario),
    FOREIGN KEY (IdUsuario2) REFERENCES Usuario(IdUsuario),
    UNIQUE (IdUsuario1, IdUsuario2)
);

CREATE TABLE PostUsuario (
    IdPost INT AUTO_INCREMENT PRIMARY KEY,
    IdUsuarioPost INT NOT NULL,
    FOREIGN KEY (IdUsuarioPost) REFERENCES Usuario(IdUsuario),
    TextoPost VARCHAR(300) NOT NULL,
    ExisteFoto BOOLEAN NOT NULL,
    TituloFoto VARCHAR(30),
    NumLikes INT,
    NumDislikes INT,
    FechaCreacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Comentario (
    IdComentario INT AUTO_INCREMENT PRIMARY KEY,
    IdComentarioPost INT,
    FOREIGN KEY (IdComentarioPost) REFERENCES PostUsuario(IdPost),
    IdComentarioUsuario INT,
    FOREIGN KEY (IdComentarioUsuario) REFERENCES Usuario(IdUsuario),
    TextoComentario VARCHAR(300)
);

ALTER TABLE PostUsuario
ADD CONSTRAINT CHK_FotoRequiereTexto CHECK (ExisteFoto = FALSE OR TituloFoto IS NOT NULL);

DELIMITER //
CREATE TRIGGER before_insert_Usuario
BEFORE INSERT ON Usuario
FOR EACH ROW
BEGIN
    IF NEW.FechaNacimiento >= CURDATE() THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'La Fecha de Nacimiento debe ser anterior a la fecha actual';
    END IF;
END;
//
DELIMITER ;