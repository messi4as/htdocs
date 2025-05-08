<?php
session_start();
require 'db_connect.php';


if (isset($_POST['cad_contratos'])) {
    $nome = htmlspecialchars($_POST['nome']);
    $data_inicial = htmlspecialchars($_POST['data_inicial']);
    $data_final = htmlspecialchars($_POST['data_final']);
    $valor = htmlspecialchars($_POST['valor']);
    $status = htmlspecialchars($_POST['status']);
    $descricao = strtoupper(htmlspecialchars($_POST['descricao']));
    $tipo_contrato = htmlspecialchars($_POST['tipo_contrato']);

 
    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['anexo'])) {
        foreach ($_FILES['anexo']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['anexo']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['anexo']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                move_uploaded_file($tmp_name, $documento_caminho);
                $documentos[] = $documento_caminho;
            }
        }
    }
    $documentos_json = json_encode($documentos);

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO contratos (nome, data_inicial, data_final, valor, status, descricao, tipo_contrato, anexo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $nome, $data_inicial, $data_final, $valor, $status, $descricao, $tipo_contrato, $documentos_json);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Contrato cadastrado com sucesso!";
        header("Location: lista_contratos.php");
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar Contrato: " . $stmt->error;
        header("Location: lista_contratos.php");
        exit(0);
    }
}

if (isset($_POST['edit_contratos'])) {
    echo "<pre>DEBUG _FILES (EDIT): ";
    var_dump($_FILES);
    echo "</pre>";

    echo "<pre>DEBUG _POST['documentos_atuais']: ";
    var_dump($_POST['documentos_atuais']);
    echo "</pre>";

    echo "<pre>DEBUG _POST['acao_documentos']: ";
    var_dump($_POST['acao_documentos']);
    echo "</pre>";

    $id = $_POST['id'];
    $nome = htmlspecialchars($_POST['nome']);
    $data_inicial = htmlspecialchars($_POST['data_inicial']);
    $data_final = htmlspecialchars($_POST['data_final']);
    $valor = htmlspecialchars($_POST['valor']);
    $status = htmlspecialchars($_POST['status']);
    $descricao = strtoupper(htmlspecialchars($_POST['descricao']));
    $tipo_contrato = htmlspecialchars($_POST['tipo_contrato']);

   

    $documentos = [];
    if (isset($_POST['acao_documentos']) && $_POST['acao_documentos'] == 'substituir') {
        // Excluir os documentos antigos
        $documentos_atuais = json_decode($_POST['documentos_atuais'], true) ?? [];
        if (!empty($documentos_atuais) && is_array($documentos_atuais)) {
            foreach ($documentos_atuais as $documento_atual) {
                if (file_exists($_SERVER['DOCUMENT_ROOT'] . $documento_atual)) {
                    unlink($_SERVER['DOCUMENT_ROOT'] . $documento_atual);
                }
            }
        }
        // Adicionar novos documentos
        if (isset($_FILES['anexo']) && count($_FILES['anexo']['tmp_name']) > 0 && $_FILES['anexo']['error'][0] == 0) {
            foreach ($_FILES['anexo']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['anexo']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['anexo']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['anexo']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = '/contratos/uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['anexo']['name'][$key];
                        header('Location: edit_contratos.php?id=' . $id);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['anexo']['name'][$key] . " - Código de erro: " . $_FILES['anexo']['error'][$key];
                    header('Location: edit_contratos.php?id=' . $id);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os documentos atuais e adicionar novos
        $documentos = json_decode($_POST['documentos_atuais'], true) ?? [];
        if (isset($_FILES['anexo']) && count($_FILES['anexo']['tmp_name']) > 0 && $_FILES['anexo']['error'][0] == 0) {
            foreach ($_FILES['anexo']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['anexo']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['anexo']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['anexo']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = '/contratos/uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'] . $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['anexo']['name'][$key];
                        header('Location: edit_contratos.php?id=' . $id);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['anexo']['name'][$key] . " - Código de erro: " . $_FILES['anexo']['error'][$key];
                    header('Location: edit_contratos.php?id=' . $id);
                    exit(0);
                }
            }
        }
    }

    $documentos_json = json_encode($documentos);

    $stmt = $conn->prepare("UPDATE contratos SET nome=?, data_inicial=?, data_final=?, valor=?, status=?, descricao=?, tipo_contrato=?, anexo=? WHERE id_contrato=?");
    $stmt->bind_param("ssssssssi", $nome, $data_inicial, $data_final, $valor, $status, $descricao, $tipo_contrato, $documentos_json, $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Contrato editado com sucesso!";
        header('Location: edit_contratos.php?id=' . $id);
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao editar Contrato: " . $stmt->error;
        header('Location: edit_contratos.php?id=' . $id);
        exit(0);
    }
}