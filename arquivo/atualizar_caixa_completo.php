<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nome = $_POST['nome_caixa'];

    // Agora atualizamos apenas o nome. 
    // Armário, Bandeja e Posição são imutáveis para manter o ID fixo.
    $stmt_upd = $conn->prepare("UPDATE caixas SET nome_caixa = ? WHERE id = ?");
    $stmt_upd->bind_param("si", $nome, $id);
    $armario_origem = $_POST['armario_origem'] ?? 1;
$id = $_POST['id'];

    if ($stmt_upd->execute()) {
header("Location: detalhes_caixa.php?id=$id&armario=$armario_origem&status=updated");    } else {
        echo "Erro ao atualizar: " . $conn->error;
    }
}
?>