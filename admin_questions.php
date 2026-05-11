<?php 
session_start();

if(!isset($_SESSION['user']) || $_SESSION['user']['userRole'] !== 'admin'){
    header("Location: index.php");
    exit();
}

require_once 'db/connection.php';

$scenario_id = isset($_GET['scenario_id']) ? (int)$_GET['scenario_id'] : 0;

$scenario = $conn->prepare("SELECT * FROM scenarios WHERE id = :id");
$scenario->execute([':id' => $scenario_id]);
$scenario = $scenario->fetch();

if(!$scenario){
    header("Location: admin.php");
    exit();
}

$questions = $conn->prepare("
    SELECT * FROM questions 
    WHERE scenario_id = :scenario_id 
    ORDER BY question_order
");
$questions->execute([':scenario_id' => $scenario_id]);
$questions = $questions->fetchAll();

if(isset($_POST['add_question'])){
    $nextOrder = count($questions) + 1;
    
    $stmt = $conn->prepare("INSERT INTO questions (scenario_id, question_text, question_order) VALUES (:scenario_id, :text, :order)");
    $stmt->execute([
        ':scenario_id' => $scenario_id,
        ':text' => $_POST['question_text'],
        ':order' => $nextOrder
    ]);
    header("Location: admin_questions.php?scenario_id=" . $scenario_id);
    exit();
}

if(isset($_POST['delete_question'])){
    $stmt = $conn->prepare("DELETE FROM questions WHERE id = :id");
    $stmt->execute([':id' => $_POST['question_id']]);
    header("Location: admin_questions.php?scenario_id=" . $scenario_id);
    exit();
}

if(isset($_POST['add_answer'])){
    $stmt = $conn->prepare("INSERT INTO answers (question_id, answer_text, score) VALUES (:question_id, :text, :score)");
    $stmt->execute([
        ':question_id' => $_POST['question_id'],
        ':text' => $_POST['answer_text'],
        ':score' => (int)$_POST['score']
    ]);
    header("Location: admin_questions.php?scenario_id=" . $scenario_id);
    exit();
}

if(isset($_POST['delete_answer'])){
    $stmt = $conn->prepare("DELETE FROM answers WHERE id = :id");
    $stmt->execute([':id' => $_POST['answer_id']]);
    header("Location: admin_questions.php?scenario_id=" . $scenario_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Вопросы: <?= htmlspecialchars($scenario['title']) ?></title>
    <link rel="stylesheet" href="style/admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>Вопросы сценария</h1>
            <h2><?= htmlspecialchars($scenario['title']) ?></h2>
            <div class="nav">
                <a href="admin.php" class="nav-btn">← Назад</a>
                <a href="index.php" class="nav-btn">Главная</a>
            </div>
        </div>
        
        <form method="POST" class="form-add">
            <input type="text" name="question_text" placeholder="Текст нового вопроса" required>
            <button type="submit" name="add_question">Добавить вопрос</button>
        </form>
        
        <?php if(empty($questions)): ?>
            <p class="empty-text">Нет вопросов. Добавьте первый вопрос выше!</p>
        <?php endif; ?>
        
        <?php foreach($questions as $q): ?>
        <div class="question-card">
            <div class="question-header">
                <span>Вопрос <?= $q['question_order'] ?>: <?= htmlspecialchars($q['question_text']) ?></span>
                <form method="POST" onsubmit="return confirm('Удалить вопрос?')">
                    <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                    <button type="submit" name="delete_question" class="btn-sm btn-red">Удалить вопрос</button>
                </form>
            </div>
            
            <?php
            $answers = $conn->prepare("SELECT * FROM answers WHERE question_id = :question_id");
            $answers->execute([':question_id' => $q['id']]);
            $answers = $answers->fetchAll();
            ?>
            
            <?php foreach($answers as $a): ?>
            <div class="answer-row">
                <span><?= htmlspecialchars($a['answer_text']) ?></span>
                <div class="answer-actions">
                    <span class="badge"><?= $a['score'] ?> баллов</span>
                    <form method="POST" onsubmit="return confirm('Удалить ответ?')">
                        <input type="hidden" name="answer_id" value="<?= $a['id'] ?>">
                        <button type="submit" name="delete_answer" class="btn-sm btn-red">Удалить</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            
            <form method="POST" class="form-add-answer">
                <input type="hidden" name="question_id" value="<?= $q['id'] ?>">
                <input type="text" name="answer_text" placeholder="Новый вариант ответа" required>
                <input type="number" name="score" class="score-input" placeholder="Баллы" required min="0" max="100">
                <button type="submit" name="add_answer" class="btn-sm btn-green">Добавить</button>
            </form>
        </div>
        <?php endforeach; ?>
        
        <div class="back-link">
            <a href="admin.php" class="nav-btn">← Вернуться к сценариям</a>
        </div>
    </div>
</body>
</html>