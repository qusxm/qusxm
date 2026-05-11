<?php
session_start();
require_once 'db/connection.php';

$scenarios = $conn->query("SELECT * FROM scenarios ORDER BY id")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Все сценарии - Экологическая система опросов</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container container-wide">
        <div class="header">
            <h1>Все экологические сценарии</h1>
            <div class="nav">
                <a href="index.php" class="nav-btn">Главная</a>
                <?php if(!isset($_SESSION['user'])): ?>
                    <a href="regPage.php" class="nav-btn">Регистрация</a>
                    <a href="authPage.php" class="nav-btn">Авторизация</a>
                <?php else: ?>
                    <a href="user.php" class="nav-btn">Личный кабинет</a>
                    <?php if($_SESSION['user']['userRole'] === 'admin'): ?>
                        <a href="admin.php" class="nav-btn admin">Админ-панель</a>
                    <?php endif; ?>
                    <a href="php/logout.php" class="nav-btn logout">Выход</a>
                <?php endif; ?>
            </div>
        </div>

        <p style="text-align: center; color: #888; margin-top: 30px; margin-bottom: 30px;">Выберите интересующий вас опрос</p>
        
        <div class="scenarios-grid">
            <?php foreach($scenarios as $scenario): ?>
            <div class="scenario-card">
                <h3><?= htmlspecialchars($scenario['title']) ?></h3>
                <p><?= htmlspecialchars($scenario['description']) ?></p>
                <?php if(isset($_SESSION['user'])): ?>
                    <a href="surveyPage.php?id=<?= $scenario['id'] ?>" class="start-btn">Пройти опрос</a>
                <?php else: ?>
                    <a href="authPage.php" class="start-btn">Авторизуйтесь</a>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>