-- Aproavena · esquema MySQL
-- Importar una sola vez:  mysql -u USUARIO -p BASE < sql/schema.sql
-- (o pegarlo en phpMyAdmin / Adminer)

SET NAMES utf8mb4;
SET time_zone = '-04:00';   -- Chile continental


-- Noticias -------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS noticias (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title        VARCHAR(255)  NOT NULL,
  summary      TEXT          NOT NULL,
  body         MEDIUMTEXT    NOT NULL,
  cover_url    VARCHAR(255)  DEFAULT NULL,
  pdf_url      VARCHAR(255)  DEFAULT NULL,
  published    TINYINT(1)    NOT NULL DEFAULT 1,
  published_at DATE          NOT NULL,
  created_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_listado (published, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Usuarios del panel ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email         VARCHAR(190) NOT NULL,
  nombre        VARCHAR(120) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uniq_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Mensajes del formulario de contacto ----------------------------------------
CREATE TABLE IF NOT EXISTS mensajes (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  nombre     VARCHAR(120) NOT NULL,
  correo     VARCHAR(190) NOT NULL,
  empresa    VARCHAR(160) DEFAULT NULL,
  mensaje    TEXT         NOT NULL,
  ip         VARCHAR(45)  DEFAULT NULL,
  enviado    TINYINT(1)   NOT NULL DEFAULT 0,  -- 1 si mail() aceptó el envío
  leido      TINYINT(1)   NOT NULL DEFAULT 0,
  created_at TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_bandeja (leido, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Contenido inicial ----------------------------------------------------------
-- Las tres noticias que hoy vive en assets/news-data.js, para que el sitio
-- arranque con contenido real en vez de vacío.

INSERT INTO noticias (title, summary, body, cover_url, pdf_url, published, published_at) VALUES
(
  'Aproavena en Campo Sureño: la industria de la avena sigue creciendo',
  'El diario Campo Sureño reseñó el desarrollo del sector avenero nacional y el rol de los procesadores asociados a Aproavena en la cadena de valor.',
  'Campo Sureño publicó una nota sobre el estado de la industria procesadora de avena en Chile, destacando el trabajo conjunto de los asociados a Aproavena en el mejoramiento de estándares de calidad e inocuidad. Puedes revisar la publicación original en el enlace descargable de esta noticia.',
  'assets/foto-campo-cielo.webp',
  'assets/noticia-campo-sureno-2022.pdf',
  1, '2022-04-18'
),
(
  'Socios de Aproavena se reúnen para revisar la temporada de siembra',
  'Representantes de las empresas asociadas analizaron proyecciones de superficie sembrada y condiciones agroclimáticas para la próxima temporada.',
  'En su reunión periódica, los asociados de Aproavena revisaron las proyecciones de superficie sembrada de avena para la temporada, junto con las condiciones agroclimáticas de las principales zonas productoras del sur de Chile. La asociación continúa trabajando en conjunto con productores para propiciar el cultivo de avena en todo el país.',
  'assets/foto-campo-agricultor.webp',
  NULL,
  1, '2024-09-03'
),
(
  'Avanza el trabajo por la calidad e inocuidad de la avena nacional',
  'Aproavena impulsa iniciativas conjuntas entre sus asociados para fortalecer los atributos de calidad de los productos elaborados en base a avena.',
  'Como parte de sus objetivos permanentes, Aproavena continúa fortaleciendo y potenciando los atributos de calidad e inocuidad de los productos elaborados a partir de avena nacional, en coordinación con sus empresas asociadas y organismos públicos y privados vinculados a la actividad.',
  'assets/foto-procesamiento-grano.webp',
  NULL,
  1, '2025-01-20'
);

-- El usuario administrador NO se crea aquí: usa  php crear-usuario.php
-- para generar el hash de la contraseña correctamente.
