<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $brinco_bezerro = mysqli_real_escape_string($conn, $_POST['brinco_bezerro']);
    $brinco_mae = mysqli_real_escape_string($conn, $_POST['brinco_mae']);
    $data_nascimento = mysqli_real_escape_string($conn, $_POST['data_nascimento']);
    $sexo_bezerro = mysqli_real_escape_string($conn, $_POST['sexo_bezerro']);
    $agrupamento = mysqli_real_escape_string($conn, $_POST['agrupamento']);
    $situacao_atual = mysqli_real_escape_string($conn, $_POST['situacao_atual']);

    // Obter o novo cod_mae
    $query_cod_mae = "SELECT cod_animal FROM bovinos WHERE brinco = '$brinco_mae' LIMIT 1";
    $result_cod_mae = mysqli_query($conn, $query_cod_mae);
    $row_cod_mae = mysqli_fetch_assoc($result_cod_mae);
    $cod_mae = $row_cod_mae['cod_animal'];

    // Atualizar a tabela bovinos
    $query_bovinos = "UPDATE bovinos b1
                      JOIN nascimentos n ON b1.cod_animal = n.cod_bezerro
                      SET b1.brinco = '$brinco_bezerro', b1.data_nascimento = '$data_nascimento', b1.sexo = '$sexo_bezerro', b1.agrupamento = '$agrupamento', b1.situacao_atual = '$situacao_atual'
                      WHERE n.id = $id";
    mysqli_query($conn, $query_bovinos);

    // Atualizar a tabela nascimentos
    $query_nascimentos = "UPDATE nascimentos
                          SET brinco_mae = '$brinco_mae', cod_mae = '$cod_mae'
                          WHERE id = $id";
    mysqli_query($conn, $query_nascimentos);

    $_SESSION['mensagem'] = "Nascimento atualizado com sucesso!";
    header('Location: view_nascimentos.php');
    exit();
}
