<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db_connect.php';
require 'funcoes_log.php';

// Verificar se o usuário está logado e se o ID do usuário está na sessão
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['id_usuarios'])) {
    $_SESSION['mensagem'] = "Autenticação necessária.";
    header('Location: lista_veiculos.php');
    exit(0);
}

// Adicione var_dump() para verificar $_SESSION['id_usuario']
echo "Valor de \$_SESSION['id_usuarios']: ";
var_dump($_SESSION['id_usuarios']);

$id_usuario_logado = $_SESSION['id_usuarios'];

// Bloco principal do processamento do formulário
if (isset($_POST['edit_veiculos'])) {
    echo "Início do processamento do formulário de edição.<br>";
    var_dump($_POST);
    var_dump($_FILES);

    // 1. Coleta de dados do formulário
    $id = $_POST['id'];
    $nome = htmlspecialchars($_POST['nome_veiculo']);
    $placa = htmlspecialchars($_POST['placa_veiculo']);
    $renavan = htmlspecialchars($_POST['renavan_veiculo']);
    $uf = htmlspecialchars($_POST['uf_veiculo']);
    $chassi = htmlspecialchars($_POST['chassi_veiculo']);
    $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
    $proprietario = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['proprietario_veiculo']));
    echo "Dados do formulário coletados.<br>";

    // 2. Processamento da foto
    $foto_caminho = $_POST['foto_atual'];
    if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
        if (file_exists($_POST['foto_atual']) && !empty($_POST['foto_atual'])) {
            unlink($_POST['foto_atual']);
        }
        $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        if (!move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho)) {
            $_SESSION['mensagem'] = "Erro ao mover a nova foto.";
            header('Location: edit_veiculos.php?id=' . $id);
            exit(0);
        }
    }
    echo "Processamento da foto concluído.<br>";

    // 3. Processamento de documentos
    $documentos_json_string = $_POST['documentos_atuais'] ?? '[]';
    $documentos = json_decode($documentos_json_string, true);
    if ($documentos === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Erro ao decodificar JSON: " . json_last_error_msg() . "<br>";
        $_SESSION['mensagem'] = "Erro ao decodificar documentos.";
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
    if (isset($_POST['acao_documentos']) && $_POST['acao_documentos'] == 'substituir') {
        if (!empty($documentos) && is_array($documentos)) {
            foreach ($documentos as $documento_atual) {
                if (file_exists($documento_atual)) {
                    unlink($documento_atual);
                }
            }
            $documentos = [];
        }
    }
    if (isset($_FILES['documentos_veiculo']) && is_array($_FILES['documentos_veiculo']['name'])) {
        foreach ($_FILES['documentos_veiculo']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_veiculo']['error'][$key] == 0) {
                $nome_arquivo = pathinfo($_FILES['documentos_veiculo']['name'][$key], PATHINFO_FILENAME);
                $extensao_arquivo = pathinfo($_FILES['documentos_veiculo']['name'][$key], PATHINFO_EXTENSION);
                $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                if (move_uploaded_file($tmp_name, $documento_caminho)) {
                    $documentos[] = $documento_caminho;
                } else {
                    $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_veiculo']['name'][$key];
                    header('Location: edit_veiculos.php?id=' . $id);
                    exit(0);
                }
            } elseif ($_FILES['documentos_veiculo']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_veiculo']['name'][$key] . " - Código de erro: " . $_FILES['documentos_veiculo']['error'][$key];
                header('Location: edit_veiculos.php?id=' . $id);
                exit(0);
            }
        }
    }
    echo "Processamento de documentos concluído.<br>";

    // 4. Preparação dos dados para o banco de dados
    $documentos_json = json_encode(array_unique($documentos));
    if ($documentos_json === false) {
        $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
    echo "JSON de documentos criado.<br>";

    // 5. Atualização do banco de dados
    $stmt = $conn->prepare("UPDATE veiculos SET nome_veiculo=?, placa_veiculo=?, renavan_veiculo=?, uf_veiculo=?, chassi_veiculo=?, marca_modelo_veiculo=?, proprietario_veiculo=?, foto_veiculo=?, documentos_veiculo=? WHERE cod_veiculo=?");
    if ($stmt === false) {
        error_log("Erro na preparação da consulta: " . $conn->error);
        $_SESSION['mensagem'] = "Erro interno do servidor.";
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
    $stmt->bind_param("sssssssssi", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json, $id);
    if ($stmt->execute()) {
        echo "Veículo editado com sucesso!<br>";
        $_SESSION['mensagem'] = "Veículo editado com sucesso!";
        header('Location: lista_veiculos.php');
        exit(0);
    } else {
        error_log("Erro na execução da consulta: " . $stmt->error);
        $_SESSION['mensagem'] = "Erro ao editar veículo: " . $stmt->error;
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
}

$conn->close();
?>