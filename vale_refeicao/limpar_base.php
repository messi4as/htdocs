<?php
require 'db_connect.php';

// Limpar tabelas de registros
$conn->query("DELETE FROM utilizacoes");
$conn->query("DELETE FROM cartoes_entregues");
$conn->query("DELETE FROM entregas");
$conn->query("DELETE FROM pessoas");

// Redefinir status dos cartões para 'estoque'
$conn->query("UPDATE cartoes SET status = 'estoque'");

// Fechar conexão
$conn->close();

echo "Base de dados limpa e pronta para novos testes!";
