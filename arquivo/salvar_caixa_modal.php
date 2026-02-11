<?php
include('db_connect.php');

$nome = $_POST['nome_caixa'];
$armario = $_POST['armario'];
$bandeja = $_POST['bandeja'];
$posicao = $_POST['posicao'];

// Verifica se o local ainda está vago (segurança)
$check = $conn->prepare("SELECT id FROM caixas WHERE armario = ? AND bandeja = ? AND posicao_na_bandeja = ?");
$check->bind_param("isi", $armario, $bandeja, $posicao);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo "<script>alert('Erro: Local já ocupado!'); window.location.href='index.php';</script>";
    exit;
}

$stmt = $conn->prepare("INSERT INTO caixas (nome_caixa, armario, bandeja, posicao_na_bandeja) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sisi", $nome, $armario, $bandeja, $posicao);

if ($stmt->execute()) {
    header("Location: index.php?status=caixa_criada");
} else {
    echo "Erro ao salvar: " . $conn->error;
}
?>