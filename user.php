<?php session_start(); ?>
<?php
if (!isset($_SESSION['user'])) {
    header("Location: authPage.php");
    exit();
}

require_once 'db/connection.php';

$userId = $_SESSION['user']['userId']; 

$stmt = $conn->prepare("
    SELECT 
        s.title AS scenario_title,
        r.passed_at AS completion_date,
        r.total_score AS score,
        r.max_score,
        r.result_label AS feedback
    FROM results r
    JOIN scenarios s ON r.scenario_id = s.id
    WHERE r.user_id = :user_id
    ORDER BY r.passed_at DESC
");
$stmt->execute(['user_id' => $userId]);
$results = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Личный кабинет</h1>
            <div class="nav">
                <a href="index.php" class="nav-btn">Главная</a>
                <a href="scenarios.php" class="nav-btn">Сценарии</a>
                <?php if (isset($_SESSION['user']['userRole']) && $_SESSION['user']['userRole'] === 'admin'): ?>
                    <a href="admin.php" class="nav-btn">Админ-панель</a>
                <?php endif; ?>
                <a href="php/logout.php" class="nav-btn">Выход</a>
            </div>
        </div>

        <main class="profile-content">

            <?php if (empty($results)): ?>
                <div class="empty-state">
                    <p>Вы ещё не проходили опросов. Начните с <a href="index.php">главной страницы</a>!</p>
                </div>
            <?php else: ?>
                <div class="results-grid">
                    <?php foreach ($results as $row): ?>
                        <div class="result-card">
                            <h3><?php echo htmlspecialchars($row['scenario_title']); ?></h3>
                            <p class="meta date"><span>Пройден:</span> <?php echo date('d.m.Y в H:i', strtotime($row['completion_date'])); ?></p>
                            <p class="meta score"><span>Баллы:</span> <strong><?php echo (int)$row['score']; ?> / <?php echo (int)$row['max_score']; ?></strong></p>
                            <?php if (!empty($row['feedback'])): ?>
                                <p class="result-text"><span>Результат:</span> <?php echo htmlspecialchars($row['feedback']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>