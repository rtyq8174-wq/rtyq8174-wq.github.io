// Общая логика отображения пользователя в шапке
(function () {
  const SESSION_KEY = "eyeballing_current_user";

  function readCurrentUser() {
    try {
      return JSON.parse(localStorage.getItem(SESSION_KEY) || "null");
    } catch {
      return null;
    }
  }

  function removeCurrentUser() {
    localStorage.removeItem(SESSION_KEY);
  }

  function updateHeaderAuth() {
    const guestNav = document.getElementById("authGuestNav");
    const userNav = document.getElementById("authUserNav");
    const logoutNav = document.getElementById("authLogoutNav");
    const userName = document.getElementById("authUserName");
    const logoutBtn = document.getElementById("logoutBtn");
    if (!guestNav || !userNav || !logoutNav || !userName || !logoutBtn) return;

    const currentUser = readCurrentUser();
    if (currentUser && currentUser.name) {
      userName.textContent = currentUser.name;
      guestNav.classList.add("d-none");
      userNav.classList.remove("d-none");
      logoutNav.classList.remove("d-none");
    } else {
      guestNav.classList.remove("d-none");
      userNav.classList.add("d-none");
      logoutNav.classList.add("d-none");
    }

    logoutBtn.addEventListener("click", () => {
      removeCurrentUser();
      window.location.href = "index.html";
    });
  }

  document.addEventListener("DOMContentLoaded", updateHeaderAuth);
})();
