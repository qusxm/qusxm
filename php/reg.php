<?php
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    http_response_code(405);
    exit();
}

session_start();

$inputs = [
    'login',
    'password',
    'email',
    'tel',
    'fio'
];

foreach ($inputs as $input){
    if(!isset($_POST[$input]) || $_POST[$input]==''){
        $_SESSION['message'] = "Заполните все поля!";
        header("Location:../index.php?stat=error");
        exit();
    }
}

$login = trim($_POST['login']);
$password = $_POST['password'];
$email = trim($_POST['email']);
$tel = trim($_POST['tel']);
$fio = $_POST['fio'];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
    $_SESSION['message'] = "Некорректный email!";
    header("Location:../index.php?stat=error");
    exit();
}

require_once '../db/connection.php';

$check = $conn->prepare("
    SELECT 
        EXISTS(SELECT 1 FROM users WHERE login = :login) as login_err, 
        EXISTS(SELECT 1 FROM users WHERE email = :email) as email_err
");

$check->execute([
    ':login' => $login,
    ':email' => $email
]);

$check = $check->fetch(PDO::FETCH_ASSOC);

$errors = [];

if ($check['login_err']){
    $errors[] = 'Логин занят!';
}

if ($check['email_err']){
    $errors[] = 'Email занят!';
}

if(!empty($errors)){
    $_SESSION['message'] = implode('. ', $errors) . '!';
    header("Location:../index.php?stat=error");
    exit();
}

$newUser = $conn->prepare("
    INSERT INTO users (login, password, email, tel, fio) 
    VALUES (:login, :password, :email, :tel, :fio)
");

$newUser->execute([
    ':login' => $login,
    ':password' => password_hash($password, PASSWORD_DEFAULT),
    ':email' => $email,
    ':tel' => $tel,
    ':fio' => $fio
]);

$_SESSION['message'] = "Регистрация успешна! :)";
header("Location:../authPage.php?stat=okey");
exit();
?>
