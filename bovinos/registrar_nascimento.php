<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brinco_bezerro = mysqli_real_escape_string($conn, $_POST['brinco_bezerro']);
    $brinco_mae = mysqli_real_escape_string($conn, $_POST['brinco_mae']);
    $data_nascimento = mysqli_real_escape_string($conn, $_POST['data_nascimento']);
    $sexo_bezerro = mysqli_real_escape_string($conn, $_POST['sexo_bezerro']);
    $agrupamento = mysqli_real_escape_string($conn, $_POST['agrupamento']);
    $situacao_atual = mysqli_real_escape_string($conn, $_POST['situacao_atual']);
    $estratificacao = mysqli_real_escape_string($conn, $_POST['estratificacao']);


    $sql = "CALL registrar_nascimento('$brinco_bezerro', '$brinco_mae', '$data_nascimento', '$sexo_bezerro', '$agrupamento', '$situacao_atual', '$estratificacao')";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensagem'] = "Nascimento registrado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao registrar nascimento: " . mysqli_error($conn);
    }

    header("Location: view_animal.php");
    exit(0);
}
