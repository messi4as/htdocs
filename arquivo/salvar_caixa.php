<?php
include('db_connect.php');

$nome = $_POST['nome_caixa'];
$armario = $_POST['armario'];
$bandeja = $_POST['bandeja'];
$posicao = $_POST['posicao'];

// Verifica se já existe uma caixa nesse local exato
$check = $conn->prepare("SELECT id FROM caixas WHERE armario = ? AND bandeja = ? AND posicao_na_bandeja = ?");
$check->bind_param("isi", $armario, $bandeja, $posicao);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "<script>alert('Erro: Já existe uma caixa nesta posição!'); history.back();</script>";
} else {
    $stmt = $conn->prepare("INSERT INTO caixas (nome_caixa, armario, bandeja, posicao_na_bandeja) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sisi", $nome, $armario, $bandeja, $posicao);
    
    if ($stmt->execute()) {
        header("Location: cadastrar_volume.php?caixa_id=" . $conn->insert_id);
    } else {
        echo "Erro ao salvar: " . $conn->error;
    }
}
?>