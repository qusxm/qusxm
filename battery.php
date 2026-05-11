<?php 
if($_SERVER['REQUEST_METHOD'] !== "POST"){
    http_response_code(405);
    exit();
}

session_start();
require_once '../db/connection.php';

if(!isset($_SESSION['user'])){
    header("Location: authPage.php");
    exit();
}

$scenario_id = 1;

$question_num = isset($_POST['q']) ? (int)$_POST['q'] : 1;

$getQuestion = $conn->prepare("SELECT * FROM questions WHERE scenario_id = :scenario_id AND question_order = :order");
$getQuestion->execute([':scenario_id' => $scenario_id, ':order' => $question_num]);
$question = $getQuestion->fetch(PDO::FETCH_ASSOC);

if(isset($_POST['answer_id'])){
    $getAnswer = $conn->prepare("SELECT * FROM answers WHERE id = :answer_id");
    $getAnswer->execute([':answer_id' => $_POST['answer_id']]);
    $answer = $getAnswer->fetch(PDO::FETCH_ASSOC);
    
    $_SESSION['answers'][$scenario_id][$question_num] = [
        'question_id' => $question['id'],
        'answer_id' => $_POST['answer_id'],
        'score' => $answer['score']
    ];
}

$next_q = $question_num + 1;

$checkNext = $conn->prepare("SELECT id FROM questions WHERE scenario_id = :scenario_id AND question_order = :order");
$checkNext->execute([':scenario_id' => $scenario_id, ':order' => $next_q]);
$hasNext = $checkNext->fetch();

if($hasNext){
    header("Location: ../batteryPage.php?q=" . $next_q);
    exit();
} else {
    $total_score = 0;
    foreach($_SESSION['answers'][$scenario_id] as $answer){
        $total_score += $answer['score'];
    }
    
    $max_score = 80;
    
    if($total_score >= 56){
        $level = "Высокий результат";
        $label = "Отлично! Вы обладаете высокой экологической культурой!";
    } elseif($total_score >= 26){
        $level = "Средний результат";
        $label = "Хороший результат! Есть к чему стремиться!";
    } else {
        $level = "Низкий результат";
        $label = "Стоит задуматься об экологии!";
    }
    
    // Удаляем старый результат
    $deleteOldResult = $conn->prepare("DELETE FROM results WHERE user_id = :user_id AND scenario_id = :scenario_id");
    $deleteOldResult->execute([
        ':user_id' => $_SESSION['user']['userId'],
        ':scenario_id' => $scenario_id
    ]);
    
    // Сохраняем новый результат
    $saveResult = $conn->prepare("INSERT INTO results (user_id, scenario_id, total_score, max_score, result_label, level) VALUES (:user_id, :scenario_id, :total_score, :max_score, :label, :level)");
    $saveResult->execute([
        ':user_id' => $_SESSION['user']['userId'],
        ':scenario_id' => $scenario_id,
        ':total_score' => $total_score,
        ':max_score' => $max_score,
        ':label' => $label,
        ':level' => $level
    ]);
    
    // Удаляем старые ответы
    $deleteOld = $conn->prepare("DELETE FROM user_answers WHERE user_id = :user_id AND scenario_id = :scenario_id");
    $deleteOld->execute([
        ':user_id' => $_SESSION['user']['userId'],
        ':scenario_id' => $scenario_id
    ]);
    
    // Сохраняем новые ответы
    foreach($_SESSION['answers'][$scenario_id] as $q_num => $answer){
        $saveAnswer = $conn->prepare("INSERT INTO user_answers (user_id, scenario_id, question_id, answer_id, answer_score) VALUES (:user_id, :scenario_id, :question_id, :answer_id, :answer_score)");
        $saveAnswer->execute([
            ':user_id' => $_SESSION['user']['userId'],
            ':scenario_id' => $scenario_id,
            ':question_id' => $answer['question_id'],
            ':answer_id' => $answer['answer_id'],
            ':answer_score' => $answer['score']
        ]);
    }
    
    $_SESSION['result'] = [
        'level' => $level,
        'label' => $label,
        'total_score' => $total_score,
        'max_score' => $max_score
    ];
    
    header("Location: ../batteryPage.php?result=1");
    exit();
}

exit();
?>