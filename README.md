# EYEBALLING — учебный сайт (HTML, CSS, Bootstrap, JavaScript)

## Лабораторная работа №3

- Собственный скрипт: `script.js` (рядом с `index.html`), подключён ко всем страницам.
- Форма обратной связи на главной странице: валидация email и телефона, `console.log`, Bootstrap Modal.
- Поиск по журналам: страница `list.html`, поле `#listSearch`.
- Внешние библиотеки: jQuery и Bootstrap (CDN).
- Веб-аналитика: Google Analytics 4 — в `<head>` каждой страницы замените `G-XXXXXXXXXX` на свой Measurement ID.

## Публикация на GitHub

1. Создайте новый репозиторий на [GitHub](https://github.com/new) (без README, если уже есть локальные файлы).
2. В папке проекта выполните:

```bash
cd "/Users/deniszuravlev/Documents/programming uni"
git init
git add .
git commit -m "Initial commit: site with script.js and analytics"
git branch -M main
git remote add origin https://github.com/ВАШ_ЛОГИН/ИМЯ_РЕПОЗИТОРИЯ.git
git push -u origin main
```

Подставьте свой URL репозитория вместо примера.

## Запуск локально

Откройте `index.html` в браузере или поднимите сервер: `python3 -m http.server 5500` и перейдите на `http://localhost:5500`.
