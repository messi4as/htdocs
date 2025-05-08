<?php
session_start();
require 'db_connect.php';

if (isset($_POST['caminho_documento']) && isset($_POST['id_veiculo']) && isset($_POST['documentos_atuais'])) {
    $caminho_documento = $_POST['caminho_documento'];
    $id_veiculo = $_POST['id_veiculo'];
    $documentos_atuais = json_decode($_POST['documentos_atuais'], true);

    // Excluir o arquivo do servidor
    if (file_exists($caminho_documento)) {
        unlink($caminho_documento);
    }

    // Atualizar a lista de documentos no banco de dados
    $documentos_atuais = array_filter($documentos_atuais, function ($doc) use ($caminho_documento) {
        return $doc !== $caminho_documento;
    });

    $documentos_json = json_encode($documentos_atuais);

    $stmt = $conn->prepare("UPDATE veiculos SET documentos_veiculo=? WHERE cod_veiculo=?");
    $stmt->bind_param("si", $documentos_json, $id_veiculo);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Documento excluído com sucesso';
    } else {
        $_SESSION['mensagem'] = 'Erro ao excluir o documento';
    }

    header('Location: edit_veiculos.php?id=' . $id_veiculo);
    exit(0);
} else {
    $_SESSION['mensagem'] = 'Dados insuficientes para excluir o documento';
    header('Location: edit_veiculos.php?id=' . $_POST['id_veiculo']);
    exit(0);
}
