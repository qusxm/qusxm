<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация - Экологическая система</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container">
        <h1>Вход в систему</h1>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message error">
                <?= $_SESSION['message'] ?>
            </div>
        <?php 
        unset($_SESSION['message']);
        endif; ?>
        
        <form action="php/auth.php" method="POST">
            <label for="login">Логин или Email:</label>
            <input type="text" required name="login" id="login" placeholder="Введите логин или email">
            
            <label for="password">Пароль:</label>
            <input type="password" required name="password" id="password" placeholder="Введите пароль">
            
            <div class="btn-submit-wrapper">
                <button type="submit">Войти</button>
            </div>
            
            <div class="form-footer">
                <p>Нет аккаунта? <a href="regPage.php">Зарегистрироваться</a></p>
            </div>
        </form>
    </div>
</body>
</html>