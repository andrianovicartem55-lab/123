<?php
session_start();

// --- НАСТРОЙКИ ---
$valid_login = 'admin';
$valid_pass  = 'Bo[5W?X2W>:Lwa1';
// -----------------

// Обработка выхода
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Обработка входа
if (isset($_POST['login'])) {
    if ($_POST['login'] === $valid_login && $_POST['password'] === $valid_pass) {
        $_SESSION['auth'] = true;
    } else {
        $error = "Неверный логин или пароль";
    }
}

// Если не авторизован - показываем форму входа
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход в панель</title>
    <style>
        body { background: #121212; color: #fff; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        form { background: #1e1e1e; padding: 30px; border-radius: 10px; width: 300px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        input { width: 100%; padding: 10px; margin: 10px 0; background: #333; border: 1px solid #444; color: #fff; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #28a745; color: #fff; border: none; cursor: pointer; font-weight: bold; }
        .error { color: #ff4444; margin-bottom: 10px; text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <h2 style="text-align:center; margin-top:0;">LOGIN</h2>
        <?php if(isset($error)) echo "<div class='error'>$error</div>"; ?>
        <input type="text" name="login" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
</body>
</html>
<?php
    exit;
}

// --- ЗОНА АДМИНИСТРАТОРА (ФАЙЛ ЗАГРУЗКИ) ---

$upload_dir = 'uploads/';
$target_file = $upload_dir . 'download.apk'; // Файл всегда будет называться так
$message = "";

if (isset($_POST['upload'])) {
    if (isset($_FILES['apk_file']) && $_FILES['apk_file']['error'] == 0) {
        // Удаляем старый файл если есть
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        // Загружаем новый
        if (move_uploaded_file($_FILES['apk_file']['tmp_name'], $target_file)) {
            $message = "<div class='success'>Файл успешно обновлен!</div>";
        } else {
            $message = "<div class='error'>Ошибка загрузки. Проверьте права на папку uploads (нужны 777).</div>";
        }
    } else {
        $message = "<div class='error'>Ошибка: файл не выбран или слишком большой.</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Загрузка APK</title>
    <style>
        body { background: #121212; color: #fff; font-family: sans-serif; padding: 20px; }
        .container { max-width: 600px; margin: 50px auto; background: #1e1e1e; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.5); }
        .btn { padding: 10px 20px; background: #28a745; color: #fff; border: none; cursor: pointer; font-size: 16px; border-radius: 5px; }
        .logout { float: right; color: #888; text-decoration: none; }
        .success { background: #28a745; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .error { background: #dc3545; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .status { margin-top: 20px; padding: 15px; background: #2c2c2c; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <a href="?logout=1" class="logout">Выйти</a>
        <h2>Загрузка APK файла</h2>
        <p>Загрузите новый файл. Он автоматически заменит старый и станет доступен на сайте.</p>
        
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="apk_file" accept=".apk" required style="margin-bottom: 20px;">
            <br>
            <button type="submit" name="upload" class="btn">Загрузить на сайт</button>
        </form>

        <div class="status">
            <strong>Текущий статус:</strong><br>
            <?php 
            if (file_exists($target_file)) {
                echo "<span style='color:#28a745'>Файл загружен</span> (" . round(filesize($target_file) / 1024 / 1024, 2) . " MB)<br>";
                echo "Последнее обновление: " . date("d.m.Y H:i:s", filemtime($target_file));
            } else {
                echo "<span style='color:#ff4444'>Файл отсутствует! Кнопка на сайте не сработает.</span>";
            }
            ?>
        </div>
    </div>
</body>
</html>