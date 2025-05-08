<?php
session_start();
require 'db_connect.php';

if (isset($_POST['caminho_comprovante']) && isset($_POST['id_financeiro']) && isset($_POST['comprovantes_atuais'])) {
    $caminho_comprovante = $_POST['caminho_comprovante'];
    $id_financeiro = $_POST['id_financeiro'];
    $comprovantes_atuais = json_decode($_POST['comprovantes_atuais'], true);

    // Excluir o arquivo do servidor
    if (file_exists($caminho_comprovante)) {
        unlink($caminho_comprovante);
    }

    // Atualizar a lista de Comprovantes no banco de dados
    $comprovantes_atuais = array_filter($comprovantes_atuais, function ($doc) use ($caminho_comprovante) {
        return $doc !== $caminho_comprovante;
    });

    $comprovantes_json = json_encode($comprovantes_atuais);

    $stmt = $conn->prepare("UPDATE financeiro SET comprovante=? WHERE cod_financeiro=?");
    $stmt->bind_param("si", $comprovantes_json, $id_financeiro);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Comprovante excluído com sucesso';
    } else {
        $_SESSION['mensagem'] = 'Erro ao excluir o Comprovante';
    }

    header('Location: edit_pagamentos.php?id=' . $id_financeiro);
    exit(0);
} else {
    $_SESSION['mensagem'] = 'Dados insuficientes para excluir o comprovante';
    header('Location: edit_pagamentos.php?id=' . $_POST['id_financeiro']);
    exit(0);
}
