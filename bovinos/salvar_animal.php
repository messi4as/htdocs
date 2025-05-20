<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $brinco = mysqli_real_escape_string($conn, $_POST['brinco']);
    $local = strtoupper(mysqli_real_escape_string($conn, $_POST['local']));
    $sexo = strtoupper(mysqli_real_escape_string($conn, $_POST['sexo']));
    $raca = strtoupper(mysqli_real_escape_string($conn, $_POST['raca']));
    $data_nascimento = mysqli_real_escape_string($conn, $_POST['data_nascimento']);
    $observacao = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['observacao'])));
    $agrupamento = strtoupper(mysqli_real_escape_string($conn, $_POST['agrupamento']));
    $situacao_atual = strtoupper(mysqli_real_escape_string($conn, $_POST['situacao_atual']));
    $tipo = strtoupper(mysqli_real_escape_string($conn, $_POST['tipo']));
    $status = strtoupper(mysqli_real_escape_string($conn, $_POST['status']));
    $lote = strtoupper(mysqli_real_escape_string($conn, $_POST['lote']));
    $estratificacao = mysqli_real_escape_string($conn, $_POST['estratificacao']);

    // Substitui \r\n por <br> antes de salvar no banco de dados
    $observacao = str_replace("\r\n", "<br>", $observacao);

    // Processar upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem_dir = 'uploads/imagens/';
        $imagem = $imagem_dir . basename($_FILES['imagem']['name']);
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem);
    } else {
        $imagem = '';
    }

    $sql = "INSERT INTO bovinos (brinco, imagem, local, sexo, raca, data_nascimento, observacao, agrupamento, situacao_atual, tipo, status, lote, estratificacao) 
            VALUES ('$brinco', '$imagem', '$local', '$sexo', '$raca', '$data_nascimento', '$observacao', '$agrupamento', '$situacao_atual', '$tipo', '$status', '$lote', '$estratificacao'. '$estratificacao')";

    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Animal Cadastrado com Sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Aninal não Cadastrado';
        header('Location: index.php');
        exit;
    }
}
