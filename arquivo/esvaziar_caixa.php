<?php
include('db_connect.php');

$id = $_GET['id'];

// 1. Opcional: Deletar volumes físicos e registros
// (Cuidado: certifique-se de que quer apagar os volumes antes de resetar a caixa)
$conn->query("DELETE FROM volumes_arquivos WHERE caixa_id = $id");

// 2. Volta o nome da caixa para o padrão (ajuste o nome conforme sua lógica)
$novo_nome = "CAIXA DISPONÍVEL (RESETADA)";
$sql = "UPDATE caixas SET nome_caixa = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $novo_nome, $id);

if ($stmt->execute()) {
    header("Location: index.php?status=cleared");
}
?>