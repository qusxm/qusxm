<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Экологическая система</title>
    <link rel="stylesheet" href="style/style.css">
    <script defer src="js/script.js"></script>
</head>
<body>
    <div class="container">
        <h1>Регистрация</h1>
        
    <form action="php/reg.php" method="POST">
        <label for="login">Логин:</label>
        <input type="text" required name="login" id="login" placeholder="Введите логин">
            
        <label for="password">Пароль:</label>
        <input type="password" required name="password" id="password" placeholder="Введите пароль">
            
        <label for="repPassword">Повторите пароль:</label>
        <input type="password" required name="repPassword" id="repPassword" placeholder="Повторите пароль">
        <p id="message"></p>

        <label for="email">Email:</label>
        <input type="email" required name="email" id="email" placeholder="Введите email">

        <div class="checkbox-group">
            <input type="checkbox" required name="accept" id="accept">
            <label for="accept">Согласие на обработку данных</label>
        </div>
            
        <div class="btn-submit-wrapper">
            <button type="submit">Зарегистрироваться</button>
        </div>
            
        <div class="form-footer">
            <p>Есть аккаунт? <a href="authPage.php">Войти</a></p>
        </div>
        </form>
    </div>
</body>
</html>