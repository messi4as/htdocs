<?php
session_start();
require 'db_connect.php';

if (isset($_POST['caminho_documento']) && isset($_POST['id_veiculo']) && isset($_POST['documentos_atuais'])) {
    $caminho_documento = $_POST['caminho_documento'];
    $id_veiculo = $_POST['id_veiculo'];
    $documentos_atuais = json_decode($_POST['documentos_atuais'], true);

    // 1. Excluir o arquivo físico do servidor
    if (file_exists($caminho_documento)) {
        unlink($caminho_documento);
    }

    // 2. Filtrar a lista para remover o documento (compatível com formato string e array)
    $documentos_filtrados = array_filter($documentos_atuais, function ($doc) use ($caminho_documento) {
        if (is_array($doc)) {
            return $doc['path'] !== $caminho_documento;
        }
        return $doc !== $caminho_documento;
    });

    // 3. Reindexar o array para evitar buracos no JSON e salvar
    $documentos_json = json_encode(array_values($documentos_filtrados));

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
    header('Location: lista_veiculos.php');
    exit(0);
}