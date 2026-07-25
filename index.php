<?php
session_start();

require_once 'init.php';

$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($auth->login()) {
        header('Location: profile.php');
        exit;
    }
    $status = $auth->getStatus() ?? '';
}

if (isset($_GET['registered'])) {
    if ($_GET['registered'] == 1){
        header('Location: index.php');
        $status = 'Регистрация успешна! Теперь войдите в систему.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <form class="mainform" action="index.php" method="post">
        <label>Логин</label>
        <input type="text" name="us_login" placeholder="Введите логин" required>
        
        <label>Пароль</label>
        <input type="password" name="us_password" placeholder="Введите пароль" required>
        
        <button type="submit">Войти</button>

        <p>
            Еще не зарегистрированы? <a href='reg.php'>Зарегистрироваться</a>
        </p>

        <?php if (!empty($status)){
            echo "<p class='status'>" . "$status" . "</p>";
        }
        $auth->clearStatus();
        ?>
    </form>
</body>
</html>