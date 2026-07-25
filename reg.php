<?php
require_once 'init.php';

$status = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($reg->createUser()) {
        header('Location: index.php?registered=1');
        exit;
    }
    $status = $reg->getStatus();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <form class="mainform" action="reg.php" method="post">
        <label>Логин</label>
        <input type="text" name="us_login" placeholder="Введите логин" required>
        
        <label>Пароль</label>
        <input type="password" name="us_password" placeholder="Введите пароль" required>
        
        <label>Подтвердите пароль</label>
        <input type="password" name="auth_password" placeholder="Подтвердите пароль" required>
        
        <button type="submit">Зарегистрироваться</button>

        <p>
            Уже есть аккаунт? <a href='index.php'>Авторизоваться</a>
        </p>

        <?php if (!empty($status)){
            echo "<p class='status'>" . "$status" . "</p>";
        }
        $auth->clearStatus();
        ?>
    </form>
</body>
</html>