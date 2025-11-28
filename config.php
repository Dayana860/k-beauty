<?php
// Подключаемся к правильному адресу MySQL
$host = '127.0.1.28';      // Адрес где висит MySQL
$user = 'root';
$pass = '';
$db = 'kbeauty_db';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);

?>