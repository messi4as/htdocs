<?php
session_start();
require 'db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brincos = $_POST['brincos'];
    $novo_local = $_POST['novo_local'];
    $novo_lote = $_POST['novo_lote'];

    // Separar os brincos por vírgulas e remover espaços em branco
    $brincosArray = array_map('trim', explode(',', $brincos));

    // Construir a consulta SQL
    $brincosPlaceholders = implode(',', array_fill(0, count($brincosArray), '?'));
    $sql = "UPDATE bovinos
            SET local = ?, lote = ?
            WHERE brinco IN ($brincosPlaceholders)";

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
    $params = array_merge([$novo_local, $novo_lote], $brincosArray);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    // Executar a consulta
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Local e lote atualizados com sucesso!";
        echo json_encode(["mensagem" => $_SESSION['mensagem']]);
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar local e lote: " . $stmt->error;
        echo json_encode(["mensagem" => $_SESSION['mensagem']]);
    }
}
