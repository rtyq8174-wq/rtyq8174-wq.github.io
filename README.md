# EYEBALLING — учебный сайт (HTML, CSS, Bootstrap, JavaScript, PHP)

## Лабораторная работа №5 (Ajax + jQuery + PHP)

Задание: интеграция формы обратной связи с PHP через **Ajax**; **GET** — полный список записей из БД; **POST** — сохранение без перезагрузки; модальное окно Bootstrap.

- [ajax.php](ajax.php): **`GET`** возвращает JSON `{ "ok": true, "items": [...] }` (все строки `feedback_messages`); **`POST`** — валидация как раньше, **`INSERT`**, затем снова полный список в **`items`** и текст в **`message`**.
- [script.js](script.js): отправка формы через **`jQuery.post`**, загрузка списка — **`jQuery.get`** на `ajax.php`; после успешной отправки показывается **Bootstrap Modal** и обновляется таблица на главной; кнопка «Обновить список» и **автообновление раз в 60 секунд**.
- Файл [save_feedback.php](save_feedback.php) оставлен (ЛР №4); на главной используется **`ajax.php`**.

## Лабораторная работа №4 (PHP + MySQL + MAMP)

### Что сделано

- База **`eyeballing_lab4`**: таблицы **`magazines`** (каталог), **`feedback_messages`** (обратная связь) и **`site_users`** (регистрация на [form.html](form.html)). Скрипт создания: [sql/schema.sql](sql/schema.sql). Если база уже была без пользователей, выполните [sql/add_site_users_table.sql](sql/add_site_users_table.sql).
- [script.php](script.php): функции **`getDb()`** (PDO) и **`getMagazines()`** — выборка списка журналов.
- [list.php](list.php): страница списка на PHP, вывод карточек циклом **`foreach`** из БД.
- [save_feedback.php](save_feedback.php): приём POST (ЛР №4); на главной для обратной связи см. ЛР №5 — [ajax.php](ajax.php) и [script.js](script.js).
- [register_user.php](register_user.php) и [login_user.php](login_user.php): регистрация (**`INSERT`** в `site_users`, пароль через **`password_hash`**) и вход (**`password_verify`**); в браузере остаётся только объект сессии **`eyeballing_current_user`** (имя, email, роль) для шапки.

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

Без PHP-сервера страница **`list.php`**, сохранение обратной связи и **регистрация/вход** **не заработают** (не открывайте сайт как `file:///`).

## Лабораторная работа №3

- Собственный скрипт: [script.js](script.js), подключён ко всем страницам.
- Форма обратной связи на главной: валидация, `console.log`, Bootstrap Modal, запись в БД через Ajax (ЛР №5: jQuery + [ajax.php](ajax.php)).
- Поиск по журналам: страница [list.php](list.php), поле `#listSearch`.
- Внешние библиотеки: jQuery и Bootstrap (CDN).
- Веб-аналитика: Google Analytics 4 — замените `G-XXXXXXXXXX` в HTML на свой Measurement ID.

## Публикация на GitHub

Файл **`config.php`** в репозиторий не коммитится (см. [.gitignore](.gitignore)). После клона скопируйте `config.example.php` в `config.php` и задайте доступ к MySQL.

```bash
git clone https://github.com/rtyq8174-wq/rtyq8174-wq.github.io.git
cd rtyq8174-wq.github.io
cp config.example.php config.php
# настройте config.php и импортируйте sql/schema.sql в MySQL (MAMP)
```

## Запуск без MAMP (только статика)

Главная и страницы журналов по-прежнему открываются как статика, но **каталог из БД** и **сохранение обратной связи** требуют Apache+PHP+MySQL (MAMP).
