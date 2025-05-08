<?php
require 'db_connect.php';

for ($i = 1; $i <= 400; $i++) {
    $numero = str_pad($i, 3, '0', STR_PAD_LEFT);
    $sql = "INSERT INTO cartoes (numero) VALUES ('$numero')";
    if ($conn->query($sql) === TRUE) {
        echo "Cartão $numero inserido com sucesso.<br>";
    } else {
        echo "Erro ao inserir cartão $numero: " . $conn->error . "<br>";
    }
}

$conn->close();
