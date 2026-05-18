<?php
declare(strict_types=1);

require_once __DIR__ . "/script.php";

$dbError = null;
$magazines = [];
try {
    $magazines = getMagazines();
} catch (Throwable $e) {
    $dbError = "Не удалось подключиться к базе данных. Импортируйте sql/schema.sql в phpMyAdmin и проверьте config.php (порт MySQL в MAMP).";
}
?>
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Веб-аналитика Google Analytics 4: замените G-XXXXXXXXXX на свой Measurement ID -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag() {
        dataLayer.push(arguments);
      }
      gtag("js", new Date());
      gtag("config", "G-XXXXXXXXXX");
    </script>
    <title>EYEBALLING | Список журналов</title>
    <link rel="icon" type="image/svg+xml" href="assets/favicon.svg" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="css/styles.css" />
  </head>
  <body>
    <!-- Шапка сайта с логотипом и меню -->
    <header class="site-header sticky-top">
      <div class="container">
        <div class="d-flex align-items-center justify-content-between py-2">
          <a class="d-flex align-items-center text-decoration-none text-dark" href="index.html">
            <img src="assets/logo.svg" alt="Логотип EYEBALLING" class="logo-icon me-2" />
            <span class="brand-title">EYEBALLING</span>
          </a>
          <nav class="navbar navbar-expand-md p-0">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainMenu">
              <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainMenu">
              <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.html">Главная</a></li>
                <li class="nav-item"><a class="nav-link active" href="list.php">Журналы</a></li>
                <li class="nav-item" id="authGuestNav"><a class="nav-link" href="form.html">Войти/Регистрация</a></li>
                <li class="nav-item d-none" id="authUserNav"><span class="nav-link" id="authUserName"></span></li>
                <li class="nav-item d-none" id="authLogoutNav"><button class="btn btn-sm btn-outline-dark ms-md-2" id="logoutBtn" type="button">Выйти</button></li>
              </ul>
            </div>
          </nav>
        </div>
      </div>
    </header>

    <main class="py-4">
      <div class="container">
        <h1 class="text-center text-uppercase mb-4">Список журналов / статей</h1>

        <?php if ($dbError !== null) : ?>
          <div class="alert alert-warning" role="alert"><?php echo htmlspecialchars($dbError, ENT_QUOTES, "UTF-8"); ?></div>
        <?php endif; ?>

        <!-- Поиск по списку (фильтрация в script.js) -->
        <div class="row justify-content-center mb-4">
          <div class="col-md-8 col-lg-6">
            <label for="listSearch" class="form-label">Поиск по названию</label>
            <input type="text" id="listSearch" class="form-control" placeholder="Начните вводить название журнала..." autocomplete="off" />
          </div>
        </div>

        <!-- Список карточек журналов из базы данных (foreach) -->
        <div class="row g-4" id="magazineList">
          <?php foreach ($magazines as $item) : ?>
            <?php
            $title = htmlspecialchars((string) $item["title"], ENT_QUOTES, "UTF-8");
            $href = htmlspecialchars((string) $item["href"], ENT_QUOTES, "UTF-8");
            $cover = htmlspecialchars((string) $item["cover_image"], ENT_QUOTES, "UTF-8");
            $dataSearch = htmlspecialchars((string) $item["data_search"], ENT_QUOTES, "UTF-8");
            ?>
            <div class="col-md-4 magazine-item" data-search="<?php echo $dataSearch; ?>">
              <div class="card mag-card h-100">
                <img src="<?php echo $cover; ?>" class="card-img-top" alt="<?php echo $title; ?>" />
                <div class="card-body text-center">
                  <h5 class="card-title"><?php echo $title; ?></h5>
                  <a href="<?php echo $href; ?>" class="btn btn-sm btn-brand">Подробнее</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </main>

    <footer class="site-footer py-3 mt-4">
      <div class="container d-flex flex-column flex-md-row justify-content-between">
        <span>© 2026 EYEBALLING</span>
        <span><span id="magazineCatalogCount"><?php echo count($magazines); ?></span> журнал(ов) в каталоге</span>
      </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/auth-ui.js"></script>
    <script src="script.js"></script>
  </body>
</html>
