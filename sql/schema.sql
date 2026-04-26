-- Лабораторная работа №4: схема БД для EYEBALLING
-- Импорт в phpMyAdmin (MAMP) или: mysql -u root -p < sql/schema.sql

CREATE DATABASE IF NOT EXISTS eyeballing_lab4
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE eyeballing_lab4;

-- Список элементов каталога (журналы на странице списка)
CREATE TABLE IF NOT EXISTS magazines (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(255) NOT NULL,
  href VARCHAR(512) NOT NULL,
  cover_image VARCHAR(512) NOT NULL,
  data_search VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Сообщения из формы обратной связи
CREATE TABLE IF NOT EXISTS feedback_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  phone VARCHAR(64) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO magazines (title, href, cover_image, data_search, sort_order) VALUES
  ('VOGUE AUSTRALIA MARCH 2020', 'vogue.html', 'assets/vogue-pages/page-001.webp', 'vogue australia march 2020', 1),
  ('BRITISH 3 2020', 'british.html', 'assets/british-pages/page-001.webp', 'british 3 2020', 2),
  ('MDA MAGAZINE', 'item.html', 'assets/mda-pages/photo_2026-03-26_21.53.57-0ec3f837-9ce1-43fa-9a63-a0c215595852.png', 'mda magazine', 3);
