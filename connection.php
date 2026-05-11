<?php
$host = "mysql:host=localhost;dbname=eco;charset=utf8";
$user = "root";
$pass = "";
$errMode = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
try {
    $conn = new PDO($host, $user, $pass, $errMode);
} catch (PDOException $e) {
    echo "Ошибка подключения к базе данных! ".$e->getMessage();
}
?>
