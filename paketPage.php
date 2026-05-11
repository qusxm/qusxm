<?php 
session_start(); 

if(!isset($_SESSION['user'])){
    header("Location: authPage.php");
    exit();
}

require_once 'db/connection.php';

$scenario_id = 2;
$question_num = isset($_GET['q']) ? (int)$_GET['q'] : 1;

$getQuestion = $conn->prepare("SELECT * FROM questions WHERE scenario_id = :scenario_id AND question_order = :order");
$getQuestion->execute([':scenario_id' => $scenario_id, ':order' => $question_num]);
$question = $getQuestion->fetch(PDO::FETCH_ASSOC);

if(!$question){
    header("Location: index.php");
    exit();
}

$getAnswers = $conn->prepare("SELECT * FROM answers WHERE question_id = :question_id");
$getAnswers->execute([':question_id' => $question['id']]);
$answers = $getAnswers->fetchAll(PDO::FETCH_ASSOC);

$totalQuestions = $conn->prepare("SELECT COUNT(*) as total FROM questions WHERE scenario_id = :scenario_id");
$totalQuestions->execute([':scenario_id' => $scenario_id]);
$total = $totalQuestions->fetch(PDO::FETCH_ASSOC);

$levelClass = '';
if(isset($_SESSION['result'])){
    if($_SESSION['result']['level'] == 'Высокий результат') $levelClass = 'high';
    elseif($_SESSION['result']['level'] == 'Средний результат') $levelClass = 'medium';
    else $levelClass = 'low';
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Опрос: Пластиковые пакеты</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
    <div class="container">
        <?php if(isset($_GET['result'])): ?>
            <h1 class="result-title">Результат опроса</h1>
            <p class="result-level <?= $levelClass ?>"><?= $_SESSION['result']['level'] ?></p>
            <p class="result-label"><?= $_SESSION['result']['label'] ?></p>
            <div class="result-score-wrapper">
                <span class="result-score">Баллы: <?= $_SESSION['result']['total_score'] ?> из <?= $_SESSION['result']['max_score'] ?></span>
            </div>
            <div class="result-actions">
                <a href="index.php" class="nav-btn">На главную</a>
            </div>
            <?php unset($_SESSION['result']); ?>
            
        <?php else: ?>
            <h1>Пластиковые пакеты</h1>
            <p>Вопрос <?= $question_num ?> из <?= $total['total'] ?></p>
            
            <form action="scen/paket.php" method="POST">
                <input type="hidden" name="q" value="<?= $question_num ?>">
                <h3><?= htmlspecialchars($question['question_text']) ?></h3>
                
                <?php foreach($answers as $answer): ?>
                    <div class="answer-item">
                        <label>
                            <input type="radio" name="answer_id" value="<?= $answer['id'] ?>" required>
                            <?= htmlspecialchars($answer['answer_text']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
                
                <div class="btn-submit-wrapper">
                    <button type="submit">Дальше</button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>