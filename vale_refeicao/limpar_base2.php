<?php
require 'db_connect.php';

// Desativa as verificações de chave estrangeira
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Exclui os registros da tabela utilizacoes que fazem referência aos cartões
$sql = "DELETE FROM utilizacoes";

if ($conn->query($sql) === TRUE) {
    echo "Registros da tabela 'utilizacoes' excluídos com sucesso.<br>";
} else {
    echo "Erro ao excluir registros da tabela 'utilizacoes': " . $conn->error . "<br>";
}

// Limpa a tabela cartoes
$sql = "TRUNCATE TABLE cartoes";

if ($conn->query($sql) === TRUE) {
    echo "Tabela 'cartoes' limpa com sucesso.<br>";
} else {
    echo "Erro ao limpar a tabela 'cartoes': " . $conn->error . "<br>";
}

// Reativa as verificações de chave estrangeira
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// Popula a tabela cartoes com novos registros de 001 a 400
for ($i = 1; $i <= 400; $i++) {
    $numero = str_pad($i, 3, '0', STR_PAD_LEFT);
    $sql = "INSERT INTO cartoes (numero, status) VALUES ('$numero', 'estoque')";
    if ($conn->query($sql) === TRUE) {
        echo "Cartão $numero inserido com sucesso.<br>";
    } else {
        echo "Erro ao inserir o cartão $numero: " . $conn->error . "<br>";
    }
}

$conn->close();
