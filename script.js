/**
 * Лабораторная работа №3: динамика DOM, обработка формы, фильтрация списка.
 * Подключён ко всем страницам сайта.
 */

(function () {
  "use strict";

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
      alert(message);
      return;
    }
    titleEl.textContent = ok ? "Сообщение отправлено" : "Ошибка в данных";
    titleEl.classList.toggle("text-success", ok);
    titleEl.classList.toggle("text-danger", !ok);
    bodyEl.textContent = message;
    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    modal.show();
  }

  /** Обработка формы обратной связи (onSubmit) */
  function initFeedbackForm() {
    const form = document.getElementById("feedbackForm");
    if (!form) return;

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

      console.log("Форма обратной связи: данные успешно обработаны", data);
      showFeedbackModal(true, "Спасибо! Ваше сообщение принято и будет рассмотрено редакцией.");
      form.reset();
    });
  }

  /** Поиск по списку журналов (страница list.html) */
  function initMagazineSearch() {
    const input = document.getElementById("listSearch");
    const items = document.querySelectorAll(".magazine-item");
    if (!input || !items.length) return;

    function filter() {
      const q = input.value.trim().toLowerCase();
      items.forEach(function (item) {
        const haystack = (item.getAttribute("data-search") || "").toLowerCase();
        const match = !q || haystack.includes(q);
        item.classList.toggle("d-none", !match);
      });
    }

    input.addEventListener("input", filter);
    input.addEventListener("change", filter);
  }

  document.addEventListener("DOMContentLoaded", function () {
    initFeedbackForm();
    initMagazineSearch();
  });
})();
