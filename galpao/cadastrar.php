<?php
session_start();
require 'db_connect.php';

if (isset($_POST['cad_itens'])) {
    $nome = htmlspecialchars($_POST['nome_item']);
    $categoria = htmlspecialchars($_POST['categoria']);
    $quantidade = htmlspecialchars($_POST['quantidade']);
    $valor = htmlspecialchars($_POST['valor']);
    $data_entrada = htmlspecialchars($_POST['data_entrada']);
    $descricao = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['descricao'])));
    $local = htmlspecialchars($_POST['local']);
    $origem = htmlspecialchars($_POST['origem']);
    $status = htmlspecialchars($_POST['status']);

    // Substitui \r\n por <br> antes de salvar no banco de dados
    $descricao = str_replace("\r\n", "<br>", $descricao);

    // Processar a foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_nome = uniqid() . '-' . $_FILES['foto']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = null;
    }

    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['anexos_documentos'])) {
        foreach ($_FILES['anexos_documentos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['anexos_documentos']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['anexos_documentos']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                move_uploaded_file($tmp_name, $documento_caminho);
                $documentos[] = $documento_caminho;
            }
        }
    }
    $documentos_json = json_encode($documentos);

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO galpao (nome_item, categoria, quantidade, valor, data_entrada, descricao, local, origem, foto, anexo_documento, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssssss", $nome, $categoria, $quantidade, $valor, $data_entrada, $descricao, $local, $origem, $foto_caminho, $documentos_json, $status);


    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Item cadastrado com sucesso!";
        header("Location: lista_item.php");
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar Item: " . $stmt->error;
        header("Location: lista_item.php");
        exit(0);
    }
}



if (isset($_POST['edit_itens'])) {
   

    $id = $_POST['id'];
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_item'])));
    $categoria = strtoupper(mysqli_real_escape_string($conn, trim($_POST['categoria'])));
    $quantidade = htmlspecialchars($_POST['quantidade']);
    $valor = htmlspecialchars($_POST['valor']);
    $data_entrada = htmlspecialchars($_POST['data_entrada']);
    $descricao = strtoupper(htmlspecialchars($_POST['descricao']));
    $local = strtoupper(mysqli_real_escape_string($conn, trim($_POST['local'])));
    $origem = strtoupper(mysqli_real_escape_string($conn, trim($_POST['origem'])));
    $status = strtoupper(mysqli_real_escape_string($conn, trim($_POST['status'])));

    

    // Processar a foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        // Excluir a foto antiga
        if (file_exists($_POST['foto_atual'])) {
            unlink($_POST['foto_atual']);
        }
        $foto_nome = uniqid() . '-' . $_FILES['foto']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = $_POST['foto_atual']; // Manter a foto atual se não houver nova foto
    }

    // Processar os documentos
    $documentos = [];
    if (isset($_POST['acao_documentos']) && $_POST['acao_documentos'] == 'substituir') {
        // Excluir os documentos antigos
        $documentos_atuais = json_decode($_POST['documentos_atuais'], true);
        if (!empty($documentos_atuais) && is_array($documentos_atuais)) {
            foreach ($documentos_atuais as $documento_atual) {
                if (file_exists($documento_atual)) {
                    unlink($documento_atual);
                }
            }
        }
        // Adicionar novos documentos
        if (isset($_FILES['anexo_documento']) && count($_FILES['anexo_documento']['tmp_name']) > 0 && $_FILES['anexo_documento']['error'][0] == 0) {
            foreach ($_FILES['anexo_documento']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['anexo_documento']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['anexo_documento']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['anexo_documento']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['anexo_documento']['name'][$key];
                        header('Location: edit_item.php?id=' . $id);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['anexo_documento']['name'][$key] . " - Código de erro: " . $_FILES['anexo_documento']['error'][$key];
                    header('Location: edit_item.php?id=' . $id);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os documentos atuais se não houver novos
        $documentos = json_decode($_POST['documentos_atuais'], true);
        if (isset($_FILES['anexo_documento']) && count($_FILES['anexo_documento']['tmp_name']) > 0 && $_FILES['anexo_documento']['error'][0] == 0) {
            foreach ($_FILES['anexo_documento']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['anexo_documento']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['anexo_documento']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['anexo_documento']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['anexo_documento']['name'][$key];
                        header('Location: edit_item.php?id=' . $id);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['anexo_documento']['name'][$key] . " - Código de erro: " . $_FILES['anexo_documento']['error'][$key];
                    header('Location: edit_item.php?id=' . $id);
                    exit(0);
                }
            }
        }
    }

    // Verificar se a decodificação JSON foi bem-sucedida
    if (json_last_error() !== JSON_ERROR_NONE) {
        $documentos = [];
    }

    $documentos_json = json_encode($documentos);

    // Verificar se a codificação JSON foi bem-sucedida
    if ($documentos_json === false) {
        $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
        header('Location: edit_emissor.php?id=' . $id_emissor);
        exit(0);
    }

    // Atualizar os dados no banco de dados
    $stmt = $conn->prepare("UPDATE galpao SET nome_item=?, categoria=?, quantidade=?, valor=?, data_entrada=?, descricao=?, local=?, origem=?, foto=?, anexo_documento=?, status=? WHERE cod_item=?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssssssssssi", $nome, $categoria, $quantidade, $valor, $data_entrada, $descricao, $local, $origem, $foto_caminho, $documentos_json, $status, $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Item editado com sucesso!";
        header('Location: edit_item.php?id=' . $id);
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao editar Item: " . $stmt->error;
        header('Location: edit_item.php?id=' . $id);
        exit(0);
    }
}

if (isset($_POST['delete_item'])) {
    $id_item = mysqli_real_escape_string($conn, $_POST['delete_item']);
    $sql = "DELETE FROM galpao WHERE cod_item = '$id_item'";
    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Item deletado com sucesso';
        header('Location: lista_item.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Item não foi deletado';
        header('Location: lista_item.php');
        exit;
    }
}
