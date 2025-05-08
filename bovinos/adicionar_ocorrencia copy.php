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

    // Verificar o array de brincos
    echo '<pre>';
    print_r($brincosArray);
    echo '</pre>';

    // Construir a consulta SQL
    $brincosPlaceholders = implode(',', array_fill(0, count($brincosArray), '?'));
    $sql = "INSERT INTO ocorrencias (cod_animal, data, local, tipo, peso, descricao)
            SELECT cod_animal, ?, ?, ?, ?, ?
            FROM bovinos
            WHERE brinco IN ($brincosPlaceholders) and status = 'ATIVO'";

    // Verificar a consulta SQL
    echo $sql;

    // Preparar a consulta
    $stmt = $conn->prepare($sql);
    $params = array_merge([$data, $local, $tipo, $peso, $descricao], $brincosArray);

    // Verificar os parâmetros
    echo '<pre>';
    print_r($params);
    echo '</pre>';

    $stmt->bind_param(str_repeat('s', count($params)), ...$params);

    // Executar a consulta
    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Ocorrência adicionada com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao adicionar ocorrência: " . $stmt->error;
    }

    // Redirecionar de volta para a página de conferência
    header('Location: view_conferencia.php');
    exit();
}
