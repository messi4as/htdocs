<?php
session_start();
header('Content-Type: application/json');

// Inclua o arquivo de conexão
include 'db_connect.php';
require 'funcoes_log.php';

// Verifique a conexão
if ($conn->connect_error) {
    error_log("Falha na conexão: " . $conn->connect_error);
    echo json_encode(['success' => false, 'message' => 'Falha na conexão com o banco de dados']);
    exit();
}

// Obtenha os dados JSON enviados
$data = json_decode(file_get_contents('php://input'), true);

// Adicione logs para verificar os dados recebidos
error_log("Dados recebidos: " . json_encode($data));

// Verifique se os dados foram recebidos corretamente
$username = $data['username'] ?? null;
$password = $data['password'] ?? null;

// Adicione logs para verificar os valores recebidos
error_log("Username: " . $username);
error_log("Password: " . $password);

// Prepare a consulta para buscar o usuário
$stmt = $conn->prepare("SELECT id_usuarios, senha FROM usuarios WHERE nome_usuario = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Adicione logs para verificar os dados do usuário
error_log("Usuário encontrado: " . json_encode($user));
error_log("Número de linhas retornadas: " . $result->num_rows);

// Verifique se o usuário foi encontrado antes de acessar a senha
if ($user) {
    error_log("Senha enviada: " . $password);
    error_log("Hash armazenado: " . $user['senha']);

    // Verifique se a senha está correta
    if (password_verify($password, $user['senha'])) {
        error_log("Autenticação bem-sucedida");
        $_SESSION['authenticated'] = true; // Definir a sessão como autenticada
        $_SESSION['id_usuarios'] = $user['id_usuarios']; // Armazenar o ID do usuário na sessão
        echo json_encode(['success' => true, 'redirect' => 'lista_veiculos.php']);
    } else {
        error_log("Autenticação falhou");
        echo json_encode(['success' => false, 'mensagem' => 'Senha incorreta']);
    }
} else {
    error_log("Usuário não encontrado");
    echo json_encode(['success' => false, 'mensagem' => 'Usuário não encontrado']);
}

// Feche a conexão
// $stmt->close();
// $conn->close();
