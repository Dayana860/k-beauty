<?php
session_start();
if (isset($_POST['login'])) {
    $login = $_POST['login'];
    $password = $_POST['password'];
    
    // Простой пароль для демо
    if ($login === 'admin' && $password === 'admin123') {
        $_SESSION['admin'] = true;
        header('Location: index.php');
        exit;
    } else {
        $error = "Неверный логин или пароль";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Вход в админку</title>
    <style>
        body { font-family: Arial; max-width: 300px; margin: 100px auto; }
        input, button { width: 100%; padding: 10px; margin: 5px 0; }
    </style>
</head>
<body>
    <h2>Вход в админку</h2>
    <?php if (isset($error)) echo "<p style='color:red'>$error</p>"; ?>
    <form method="POST">
        <input type="text" name="login" placeholder="Логин" required>
        <input type="password" name="password" placeholder="Пароль" required>
        <button type="submit">Войти</button>
    </form>
</body>
</html>