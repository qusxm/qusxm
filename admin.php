<?php 
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['userRole'] !== 'admin'){
    header("Location: index.php");
    exit();
}

require_once 'db/connection.php';

// Получаем данные
$users = $conn->query("SELECT id, login, email, role FROM users ORDER BY id")->fetchAll();
$scenarios = $conn->query("SELECT * FROM scenarios ORDER BY id")->fetchAll();
$results = $conn->query("
    SELECT r.*, u.login, s.title 
    FROM results r
    JOIN users u ON r.user_id = u.id
    JOIN scenarios s ON r.scenario_id = s.id
    ORDER BY r.passed_at DESC
")->fetchAll();

// Обработка добавления сценария
$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_scenario'])){
    $stmt = $conn->prepare("INSERT INTO scenarios (title, description) VALUES (:title, :description)");
    $stmt->execute([':title' => $_POST['title'], ':description' => $_POST['description']]);
    $message = "Сценарий добавлен!";
    header("Refresh:0");
}

// Обработка удаления сценария
if(isset($_POST['delete_scenario'])){
    $scenario_id = $_POST['scenario_id'];
    
    $stmt = $conn->prepare("DELETE FROM user_answers WHERE scenario_id = :id");
    $stmt->execute([':id' => $scenario_id]);
    
    $stmt = $conn->prepare("DELETE FROM results WHERE scenario_id = :id");
    $stmt->execute([':id' => $scenario_id]);
    
    $stmt = $conn->prepare("SELECT id FROM questions WHERE scenario_id = :id");
    $stmt->execute([':id' => $scenario_id]);
    $question_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if(!empty($question_ids)){
        $placeholders = implode(',', array_fill(0, count($question_ids), '?'));
        $stmt = $conn->prepare("DELETE FROM answers WHERE question_id IN ($placeholders)");
        $stmt->execute($question_ids);
    }
    
    $stmt = $conn->prepare("DELETE FROM questions WHERE scenario_id = :id");
    $stmt->execute([':id' => $scenario_id]);
    
    $stmt = $conn->prepare("DELETE FROM scenarios WHERE id = :id");
    $stmt->execute([':id' => $scenario_id]);
    
    $message = "Сценарий удалён!";
    header("Refresh:0");
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style/admin.css">

</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>Админ-панель</h1>
            <div class="nav">
                <a href="index.php" class="nav-btn">Главная</a>
                <a href="scenarios.php" class="nav-btn">Сценарии</a>
                <a href="php/logout.php" class="nav-btn">Выход</a>
            </div>
        </div>
        
        <?php if($message): ?>
            <div class="message success"><?= $message ?></div>
        <?php endif; ?>
        
        <div class="stat-numbers">
            <div class="stat-item">
                <span><?= count($users) ?></span>
                Пользователей
            </div>
            <div class="stat-item">
                <span><?= count($scenarios) ?></span>
                Сценариев
            </div>
            <div class="stat-item">
                <span><?= count($results) ?></span>
                Опросов пройдено
            </div>
        </div>
        
        <h3>Сценарии</h3>
        
        <form method="POST" class="form-add">
            <input type="text" name="title" placeholder="Название сценария" required>
            <textarea name="description" placeholder="Описание" rows="1"></textarea>
            <button type="submit" name="add_scenario">Добавить</button>
        </form>
        
        <?php foreach($scenarios as $scenario): ?>
        <div class="scenario-card">
            <h4><?= htmlspecialchars($scenario['title']) ?></h4>
            <p><?= htmlspecialchars($scenario['description']) ?></p>
            <div class="scenario-actions">
                <a href="admin_questions.php?scenario_id=<?= $scenario['id'] ?>" class="btn-green">Вопросы</a>
                <form method="POST" onsubmit="return confirm('Удалить?')" style="display:inline;">
                    <input type="hidden" name="scenario_id" value="<?= $scenario['id'] ?>">
                    <button type="submit" name="delete_scenario" class="btn-red">Удалить</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
        
        <h3>Пользователи</h3>
        
        <?php foreach($users as $user): ?>
        <div class="user-line">
            <div>
                <strong><?= htmlspecialchars($user['login']) ?></strong><br>
                <small><?= htmlspecialchars($user['email']) ?></small>
            </div>
            <div>
                <span class="badge <?= $user['role'] == 'admin' ? 'badge-admin' : '' ?>">
                    <?= $user['role'] == 'admin' ? 'Админ' : 'Пользователь' ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
        
        <h3>Результаты опросов</h3>
        
        <?php foreach($results as $res): ?>
        <div class="result-line">
            <div>
                <strong><?= htmlspecialchars($res['login']) ?></strong> — 
                <?= htmlspecialchars($res['title']) ?>
                <br>
                <small><?= htmlspecialchars($res['result_label']) ?></small>
                <br>
                <small class="result-date"><?= date('d.m.Y в H:i', strtotime($res['passed_at'])) ?></small>
            </div>
            <div>
                <span class="badge"><?= $res['total_score'] ?>/<?= $res['max_score'] ?> баллов</span>
            </div>
        </div>
        <?php endforeach; ?>
        
    </div>
</body>
</html>