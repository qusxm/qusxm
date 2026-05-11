<?php session_start(); ?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Экологическая система опросов</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Экологическая система опросов</h1>
            <p>Повышаем экологическую осознанность вместе!</p>  
            <div class="nav">
                <a href="scenarios.php" class="nav-btn">Сценарии</a>
                
                <?php if(!isset($_SESSION['user'])): ?>
                    <a href="regPage.php" class="nav-btn">Регистрация</a>
                    <a href="authPage.php" class="nav-btn">Вход</a>
                <?php else: ?>
                    <a href="user.php" class="nav-btn">Личный кабинет</a> 
                    <?php if($_SESSION['user']['userRole'] === 'admin'): ?>
                        <a href="admin.php" class="nav-btn">Админ-панель</a>
                    <?php endif; ?>
                    <a href="php/logout.php" class="nav-btn">Выход</a>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="welcome-block">
            <p>Добро пожаловать в систему экологических опросов!</p>
            <p>Здесь вы можете проверить свои знания об экологии, узнать полезную информацию и внести вклад в сохранение окружающей среды.</p>
        </div>
        
        <div class="footer-note">
            <p>Проходите опросы, получайте результаты и повышайте свою экологическую культуру!</p>
        </div>
    </div>
</body>
</html>