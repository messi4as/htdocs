<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = mysqli_real_escape_string($conn, $_POST['data']);
    $local = strtoupper(mysqli_real_escape_string($conn, $_POST['local']));
    $tipo = strtoupper(mysqli_real_escape_string($conn, $_POST['tipo']));
    $descricao = strtoupper(mysqli_real_escape_string($conn, $_POST['descricao']));
    $cod_animal = mysqli_real_escape_string($conn, $_POST['cod_animal']);

    // Verificar se o peso foi fornecido
    $peso = !empty($_POST['peso']) ? "'" . mysqli_real_escape_string($conn, $_POST['peso']) . "'" : 'NULL';

    $query = "INSERT INTO ocorrencias (cod_animal, data, local, tipo, peso, descricao) 
              VALUES ('$cod_animal', '$data', '$local', '$tipo', $peso, '$descricao')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'ocorrencia' => ['data' => $data, 'local' => $local, 'tipo' => $tipo, 'peso' => $peso, 'descricao' => $descricao]]);
    } else {
        echo json_encode(['success' => false]);
    }
}
