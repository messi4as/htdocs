<?php
$servername = "localhost";
$username = "m2";
$password = "mm311287";
$dbname = "escritorio_m2";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);


// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}
