<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cod_animal = mysqli_real_escape_string($conn, $_POST['cod_animal']);
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
    $pasto = strtoupper(mysqli_real_escape_string($conn, $_POST['pasto']));
    $estratificacao = mysqli_real_escape_string($conn, $_POST['estratificacao']);


    // Substitui \r\n por <br> antes de salvar no banco de dados
    $observacao = str_replace("\r\n", "<br>", $observacao);
    

    // Processar upload da imagem
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $imagem_dir = 'uploads/imagens/';
        $imagem = $imagem_dir . basename($_FILES['imagem']['name']);

        // Excluir a imagem anterior, se existir
        if (isset($_POST['imagem_atual']) && file_exists($_POST['imagem_atual'])) {
            unlink($_POST['imagem_atual']);
        }

        // Mover a nova imagem para o diretório correto
        move_uploaded_file($_FILES['imagem']['tmp_name'], $imagem);
    } else {
        $imagem = mysqli_real_escape_string($conn, $_POST['imagem_atual']);
    }

    $sql = "UPDATE bovinos SET brinco='$brinco', imagem='$imagem', local='$local', sexo='$sexo', raca='$raca', data_nascimento='$data_nascimento', observacao='$observacao', agrupamento='$agrupamento', situacao_atual='$situacao_atual', tipo='$tipo', status='$status', lote='$lote', pasto='$pasto', estratificacao='$estratificacao' WHERE cod_animal='$cod_animal'";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['mensagem'] = "Animal atualizado com sucesso!";
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar animal: " . mysqli_error($conn);
    }

    header("Location: index.php");
    exit(0);
}
