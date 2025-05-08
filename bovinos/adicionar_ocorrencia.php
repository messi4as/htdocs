<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brincos = $_POST['brincos'];
    $data = $_POST['data'];
    $local = $_POST['local'];
    $tipo = $_POST['tipo'];
    $peso = $_POST['peso'];
    $descricao = $_POST['descricao'];

    // Separar os brincos por vírgulas e remover espaços em branco
    $brincosArray = array_map('trim', explode(',', $brincos));

    // Construir a consulta SQL
    $brincosPlaceholders = implode(',', array_fill(0, count($brincosArray), '?'));
    $sql = "INSERT INTO ocorrencias (cod_animal, data, local, tipo, peso, descricao)
            SELECT cod_animal, ?, ?, ?, ?, ?
            FROM bovinos
            WHERE brinco IN ($brincosPlaceholders) and status = 'ATIVO'";

    $stmt = $conn->prepare($sql);
    $params = array_merge([$data, $local, $tipo, $peso, $descricao], $brincosArray);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    // Enviar resposta JSON
    header('Content-Type: application/json');
    if ($stmt->execute()) {
        echo json_encode(['mensagem' => 'Ocorrência adicionada com sucesso!']);
    } else {
        echo json_encode(['mensagem' => 'Erro ao adicionar ocorrência: ' . $stmt->error]);
    }
    exit();
}