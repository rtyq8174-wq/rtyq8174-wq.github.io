# EYEBALLING — учебный сайт (HTML, CSS, Bootstrap, JavaScript, PHP)

## Лабораторная работа №4 (PHP + MySQL + MAMP)

### Что сделано

- База **`eyeballing_lab4`**: таблицы **`magazines`** (каталог) и **`feedback_messages`** (обратная связь). Скрипт создания: [sql/schema.sql](sql/schema.sql).
- [script.php](script.php): функции **`getDb()`** (PDO) и **`getMagazines()`** — выборка списка журналов.
- [list.php](list.php): страница списка на PHP, вывод карточек циклом **`foreach`** из БД.
- [save_feedback.php](save_feedback.php): приём POST, валидация, **`INSERT`** в `feedback_messages`; главная отправляет данные через `fetch` из [script.js](script.js).

### Настройка MAMP

1. Установите и запустите **MAMP** (Apache + MySQL).
2. **Web Server → Document Root** укажите на папку этого проекта (или скопируйте проект в `htdocs` MAMP).
3. Запустите серверы. Откройте сайт, например: `http://localhost:8888/` (порт Apache смотрите в MAMP → *Preferences → Ports*).
4. В том же окне посмотрите **порт MySQL** (часто **8889**). Если другой — поправьте `db_port` в `config.php`.
5. В **phpMyAdmin** (обычно `http://localhost:8888/phpMyAdmin/`) импортируйте файл [sql/schema.sql](sql/schema.sql) или выполните его в консоли MySQL.
6. Создайте **`config.php`** из шаблона (если файла ещё нет):

```bash
cp config.example.php config.php
```

Отредактируйте `config.php` при необходимости (логин/пароль root в MAMP по умолчанию часто `root` / `root`).

Без PHP-сервера страница **`list.php`** и сохранение формы **не заработают** (не открывайте сайт как `file:///`).

## Лабораторная работа №3

- Собственный скрипт: [script.js](script.js), подключён ко всем страницам.
- Форма обратной связи на главной: валидация, `console.log`, Bootstrap Modal, запись в БД (ЛР №4).
- Поиск по журналам: страница [list.php](list.php), поле `#listSearch`.
- Внешние библиотеки: jQuery и Bootstrap (CDN).
- Веб-аналитика: Google Analytics 4 — замените `G-XXXXXXXXXX` в HTML на свой Measurement ID.

## Публикация на GitHub

Файл **`config.php`** в репозиторий не коммитится (см. [.gitignore](.gitignore)). После клона скопируйте `config.example.php` в `config.php` и задайте доступ к MySQL.

```bash
cd "/Users/deniszuravlev/Documents/programming uni"
git add .
git commit -m "Lab 4: PHP, MySQL, list.php, save_feedback.php"
git remote add origin https://github.com/ВАШ_ЛОГИН/ИМЯ_РЕПОЗИТОРИЯ.git
git push -u origin main
```

## Запуск без MAMP (только статика)

Главная и страницы журналов по-прежнему открываются как статика, но **каталог из БД** и **сохранение обратной связи** требуют Apache+PHP+MySQL (MAMP).
