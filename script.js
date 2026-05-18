/**
 * Лабораторная работа №3: динамика DOM, обработка формы, фильтрация списка.
 * Лабораторная работа №5: обратная связь через jQuery Ajax → ajax.php (GET / POST).
 */

(function () {
  "use strict";

  var FEEDBACK_AJAX_URL = "ajax.php";
  var MAGAZINES_JSON_URL = "magazines_json.php";
  var MAGAZINE_LIST_REFRESH_MS = 20000;

  /** Простая проверка email */
  function isValidEmail(value) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(value).trim());
  }

  /** Проверка мобильного телефона (РФ: +7, 8 или 10 цифр с 9) */
  function isValidPhone(value) {
    const digits = String(value).replace(/\D/g, "");
    if (digits.length === 11 && (digits.startsWith("7") || digits.startsWith("8"))) {
      return digits[1] === "9";
    }
    if (digits.length === 10 && digits.startsWith("9")) return true;
    return false;
  }

  /** Показ Bootstrap Modal с текстом (успех или ошибка) */
  function showFeedbackModal(ok, message) {
    const modalEl = document.getElementById("feedbackResultModal");
    const titleEl = document.getElementById("feedbackModalTitle");
    const bodyEl = document.getElementById("feedbackModalBody");
    if (!modalEl || !titleEl || !bodyEl || typeof bootstrap === "undefined") {
      window.alert(message);
      return;
    }
    titleEl.textContent = ok ? "Сообщение отправлено" : "Ошибка в данных";
    titleEl.classList.toggle("text-success", ok);
    titleEl.classList.toggle("text-danger", !ok);
    bodyEl.textContent = message;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }

  /** Безопасная отрисовка строк таблицы (только textContent) */
  function renderFeedbackList(items) {
    const tbody = document.getElementById("feedbackAjaxList");
    if (!tbody) return;

    while (tbody.firstChild) {
      tbody.removeChild(tbody.firstChild);
    }

    if (!items || !items.length) {
      const tr = document.createElement("tr");
      const td = document.createElement("td");
      td.colSpan = 6;
      td.className = "text-muted text-center py-4";
      td.textContent = "Пока нет сообщений.";
      tr.appendChild(td);
      tbody.appendChild(tr);
      return;
    }

    items.forEach(function (row) {
      const tr = document.createElement("tr");
      ["id", "created_at", "name", "email", "phone", "message"].forEach(function (key) {
        const td = document.createElement("td");
        td.textContent = row[key] != null ? String(row[key]) : "";
        tr.appendChild(td);
      });
      tbody.appendChild(tr);
    });
  }

  /** GET ajax.php — полный список сообщений из БД */
  function loadFeedbackList() {
    if (typeof window.jQuery === "undefined") return;

    window.jQuery.get(FEEDBACK_AJAX_URL, function (json) {
      if (json && json.ok && Array.isArray(json.items)) {
        renderFeedbackList(json.items);
      } else {
        renderFeedbackList([]);
        if (json && json.message) {
          console.log("Ajax GET:", json.message);
        }
      }
    }, "json").fail(function (xhr, status, err) {
      console.log("Ajax GET: ошибка сети или сервера", status, err);
      renderFeedbackList([]);
    });
  }

  /** Кнопка «Обновить список» и таймер 60 с (только на главной, если есть блок) */
  function initFeedbackAjaxBlock() {
    const tbody = document.getElementById("feedbackAjaxList");
    const btn = document.getElementById("feedbackAjaxRefreshBtn");
    if (!tbody || typeof window.jQuery === "undefined") return;

    loadFeedbackList();

    if (btn) {
      btn.addEventListener("click", function () {
        loadFeedbackList();
      });
    }

    window.setInterval(loadFeedbackList, 60000);
  }

  /** Обработка формы обратной связи: валидация + jQuery POST → ajax.php (ЛР №5) */
  function initFeedbackForm() {
    const form = document.getElementById("feedbackForm");
    if (!form || typeof window.jQuery === "undefined") return;

    form.addEventListener("submit", function (event) {
      event.preventDefault();

      const name = document.getElementById("feedbackName");
      const email = document.getElementById("feedbackEmail");
      const phone = document.getElementById("feedbackPhone");
      const message = document.getElementById("feedbackMessage");

      const data = {
        name: name ? name.value.trim() : "",
        email: email ? email.value.trim() : "",
        phone: phone ? phone.value.trim() : "",
        message: message ? message.value.trim() : "",
      };

      if (!data.name || !data.email || !data.phone || !data.message) {
        console.log("Форма обратной связи: не заполнены обязательные поля", data);
        showFeedbackModal(false, "Заполните все обязательные поля формы.");
        return;
      }

      if (!isValidEmail(data.email)) {
        console.log("Форма обратной связи: некорректный email", data);
        showFeedbackModal(false, "Укажите корректный адрес электронной почты.");
        return;
      }

      if (!isValidPhone(data.phone)) {
        console.log("Форма обратной связи: некорректный телефон", data);
        showFeedbackModal(
          false,
          "Укажите корректный номер мобильного телефона (например, +79991234567 или 89991234567)."
        );
        return;
      }

      window.jQuery.post(FEEDBACK_AJAX_URL, data, function (json) {
        console.log("Форма обратной связи: ответ сервера", json);
        if (json && json.ok) {
          showFeedbackModal(true, json.message || "Сообщение сохранено.");
          form.reset();
          if (Array.isArray(json.items)) {
            renderFeedbackList(json.items);
          }
        } else {
          showFeedbackModal(false, (json && json.message) || "Не удалось сохранить сообщение.");
        }
      }, "json").fail(function (xhr, status, err) {
        console.log("Форма обратной связи: сеть или сервер", status, err);
        showFeedbackModal(
          false,
          "Не удалось связаться с сервером. Убедитесь, что сайт открыт через MAMP (PHP), а не как файл с диска."
        );
      });
    });
  }

  /** Поиск по списку журналов (страница list.php); элементы перезапрашиваются после автообновления каталога */
  function initMagazineSearch() {
    const input = document.getElementById("listSearch");
    if (!input) return;

    function filter() {
      const q = input.value.trim().toLowerCase();
      document.querySelectorAll("#magazineList .magazine-item").forEach(function (item) {
        const haystack = (item.getAttribute("data-search") || "").toLowerCase();
        const match = !q || haystack.includes(q);
        item.classList.toggle("d-none", !match);
      });
    }

    input.addEventListener("input", filter);
    input.addEventListener("change", filter);
  }

  function applyMagazineSearchFilter() {
    const input = document.getElementById("listSearch");
    if (!input) return;
    const q = input.value.trim().toLowerCase();
    document.querySelectorAll("#magazineList .magazine-item").forEach(function (item) {
      const haystack = (item.getAttribute("data-search") || "").toLowerCase();
      const match = !q || haystack.includes(q);
      item.classList.toggle("d-none", !match);
    });
  }

  function buildMagazineCardEl(item) {
    const col = document.createElement("div");
    col.className = "col-md-4 magazine-item";
    col.setAttribute("data-search", item.data_search != null ? String(item.data_search) : "");

    const card = document.createElement("div");
    card.className = "card mag-card h-100";

    const img = document.createElement("img");
    img.className = "card-img-top";
    img.src = item.cover_image != null ? String(item.cover_image) : "";
    img.alt = item.title != null ? String(item.title) : "";

    const body = document.createElement("div");
    body.className = "card-body text-center";

    const title = document.createElement("h5");
    title.className = "card-title";
    title.textContent = item.title != null ? String(item.title) : "";

    const link = document.createElement("a");
    link.href = item.href != null ? String(item.href) : "#";
    link.className = "btn btn-sm btn-brand";
    link.textContent = "Подробнее";

    body.appendChild(title);
    body.appendChild(link);
    card.appendChild(img);
    card.appendChild(body);
    col.appendChild(card);
    return col;
  }

  /** Каждые 20 с подтягивает каталог из БД и перерисовывает карточки без перезагрузки */
  function initMagazineCatalogAutoRefresh() {
    const listRoot = document.getElementById("magazineList");
    if (!listRoot || typeof window.jQuery === "undefined") return;

    function refresh() {
      window.jQuery.get(MAGAZINES_JSON_URL, function (json) {
        if (!json || !json.ok || !Array.isArray(json.items)) return;

        while (listRoot.firstChild) {
          listRoot.removeChild(listRoot.firstChild);
        }

        json.items.forEach(function (row) {
          listRoot.appendChild(buildMagazineCardEl(row));
        });

        const countEl = document.getElementById("magazineCatalogCount");
        if (countEl) {
          countEl.textContent = String(json.items.length);
        }

        applyMagazineSearchFilter();
      }, "json");
    }

    window.setInterval(refresh, MAGAZINE_LIST_REFRESH_MS);
  }

  document.addEventListener("DOMContentLoaded", function () {
    initFeedbackForm();
    initMagazineSearch();
    initMagazineCatalogAutoRefresh();
    initFeedbackAjaxBlock();
  });
})();
