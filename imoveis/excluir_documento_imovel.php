<?php
session_start();
require 'db_connect.php';

if (isset($_POST['caminho_documento']) && isset($_POST['id_imovel']) && isset($_POST['documentos_atuais'])) {
    $caminho_documento = $_POST['caminho_documento'];
    $id_imovel = $_POST['id_imovel'];
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

    $stmt = $conn->prepare("UPDATE imoveis SET documentos_imovel=? WHERE cod_imovel=?");
    $stmt->bind_param("si", $documentos_json, $id_imovel);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Documento excluído com sucesso';
    } else {
        $_SESSION['mensagem'] = 'Erro ao excluir o documento';
    }

    header('Location: edit_imoveis.php?id=' . $id_imovel);
    exit(0);
} else {
    $_SESSION['mensagem'] = 'Dados insuficientes para excluir o documento';
    header('Location: edit_imoveis.php?id=' . $_POST['id_imovel']);
    exit(0);
}
