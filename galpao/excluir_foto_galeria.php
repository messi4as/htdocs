<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id']) && isset($_GET['item'])) {
    $id_foto = mysqli_real_escape_string($conn, $_GET['id']);
    $id_item = mysqli_real_escape_string($conn, $_GET['item']);

    // 1. Procurar o caminho do arquivo para removê-lo do servidor
    $sql_busca = "SELECT caminho_imagem FROM galpao_imagens WHERE id = '$id_foto'";
    $resultado = mysqli_query($conn, $sql_busca);

    if ($resultado && mysqli_num_rows($resultado) > 0) {
        $foto = mysqli_fetch_assoc($resultado);
        $caminho_arquivo = $foto['caminho_imagem'];

        // 2. Apagar o arquivo físico da pasta uploads
        if (file_exists($caminho_arquivo)) {
            unlink($caminho_arquivo);
        }

        // 3. Apagar o registo no banco de dados
        $sql_delete = "DELETE FROM galpao_imagens WHERE id = '$id_foto'";
        
        if (mysqli_query($conn, $sql_delete)) {
            $_SESSION['mensagem'] = "Foto removida da galeria!";
        } else {
            $_SESSION['mensagem'] = "Erro ao remover registo do banco de dados.";
        }
    } else {
        $_SESSION['mensagem'] = "Foto não encontrada.";
    }

    // Redireciona de volta para a página de edição do item
    header("Location: edit_item.php?id=" . $id_item);
    exit(0);
} else {
    header("Location: lista_item.php");
    exit(0);
}