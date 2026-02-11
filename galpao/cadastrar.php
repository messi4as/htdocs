<?php
session_start();
require 'db_connect.php';

// --- LÓGICA DE CADASTRO NOVO ---
if (isset($_POST['cad_itens'])) {
    $nome = htmlspecialchars($_POST['nome_item']);
    $categoria = htmlspecialchars($_POST['categoria']);
    $quantidade = htmlspecialchars($_POST['quantidade']);
    $valor = htmlspecialchars($_POST['valor']);
    $data_entrada = htmlspecialchars($_POST['data_entrada']);
    $local = htmlspecialchars($_POST['local']);
    $origem = htmlspecialchars($_POST['origem']);
    $status = htmlspecialchars($_POST['status']);

    // AJUSTE DA DESCRIÇÃO: Primeiro escapa, depois converte a quebra de linha real em <br>
    $descricao_pura = mysqli_real_escape_string($conn, $_POST['descricao']);
    $descricao = nl2br($descricao_pura);

    // Processar a foto principal
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_nome = uniqid() . '-' . $_FILES['foto']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = null;
    }

    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['anexo_documento'])) {
        foreach ($_FILES['anexo_documento']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['anexo_documento']['error'][$key] == 0) {
                $doc_nome = uniqid() . '-' . $_FILES['anexo_documento']['name'][$key];
                $doc_caminho = 'uploads/documentos/' . $doc_nome;
                if (move_uploaded_file($tmp_name, $doc_caminho)) {
                    $documentos[] = $doc_caminho;
                }
            }
        }
    }
    $documentos_json = json_encode($documentos);

    $sql = "INSERT INTO galpao (nome_item, categoria, quantidade, valor, data_entrada, descricao, local, origem, foto, anexo_documento, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssss", $nome, $categoria, $quantidade, $valor, $data_entrada, $descricao, $local, $origem, $foto_caminho, $documentos_json, $status);

    if ($stmt->execute()) {
        $id_novo_item = $conn->insert_id;

        // SALVAR FOTOS NA GALERIA (Múltiplas)
        if (isset($_FILES['galeria'])) {
            foreach ($_FILES['galeria']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['galeria']['error'][$key] == 0) {
                    $nome_galeria = uniqid() . '-' . $_FILES['galeria']['name'][$key];
                    $caminho_galeria = 'uploads/fotos/' . $nome_galeria;
                    if (move_uploaded_file($tmp_name, $caminho_galeria)) {
                        $stmt_g = $conn->prepare("INSERT INTO galpao_imagens (id_item, caminho_imagem) VALUES (?, ?)");
                        $stmt_g->bind_param("is", $id_novo_item, $caminho_galeria);
                        $stmt_g->execute();
                    }
                }
            }
        }

        $_SESSION['mensagem'] = "Item cadastrado com sucesso!";
        header('Location: lista_item.php');
        exit(0);
    }
}

// --- LÓGICA DE EDIÇÃO ---
if (isset($_POST['edit_itens'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nome = htmlspecialchars($_POST['nome_item']);
    $categoria = htmlspecialchars($_POST['categoria']);
    $quantidade = htmlspecialchars($_POST['quantidade']);
    $valor = htmlspecialchars($_POST['valor']);
    $data_entrada = htmlspecialchars($_POST['data_entrada']);
    $local = htmlspecialchars($_POST['local']);
    $origem = htmlspecialchars($_POST['origem']);
    $status = htmlspecialchars($_POST['status']);

    // MUDANÇA AQUI: Pegamos o texto puro do POST. 
    // O mysqli_prepare (que você já usa abaixo com bind_param) já protege contra SQL Injection.
    $descricao = $_POST['descricao']; 

    // Lógica da Foto Principal
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $foto_nome = uniqid() . '-' . $_FILES['foto']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = $_POST['foto_atual'];
    }

    // Lógica da Galeria (Múltiplas Fotos)
    if (isset($_FILES['galeria'])) {
        foreach ($_FILES['galeria']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['galeria']['error'][$key] == 0) {
                $nome_galeria = uniqid() . '-' . $_FILES['galeria']['name'][$key];
                $caminho_galeria = 'uploads/fotos/' . $nome_galeria;
                if (move_uploaded_file($tmp_name, $caminho_galeria)) {
                    $stmt_g = $conn->prepare("INSERT INTO galpao_imagens (id_item, caminho_imagem) VALUES (?, ?)");
                    $stmt_g->bind_param("is", $id, $caminho_galeria);
                    $stmt_g->execute();
                }
            }
        }
    }

    // Documentos
    $documentos_atuais = json_decode($_POST['documentos_atuais'], true) ?? [];
    if (isset($_FILES['anexo_documento'])) {
        foreach ($_FILES['anexo_documento']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['anexo_documento']['error'][$key] == 0) {
                $doc_nome = uniqid() . '-' . $_FILES['anexo_documento']['name'][$key];
                $doc_caminho = 'uploads/documentos/' . $doc_nome;
                if (move_uploaded_file($tmp_name, $doc_caminho)) {
                    $documentos_atuais[] = $doc_caminho;
                }
            }
        }
    }
    $documentos_json = json_encode($documentos_atuais);

    // O bind_param cuida de tudo. Se o texto tem quebra de linha, ele salva quebra de linha.
    $stmt = $conn->prepare("UPDATE galpao SET nome_item=?, categoria=?, quantidade=?, valor=?, data_entrada=?, descricao=?, local=?, origem=?, foto=?, anexo_documento=?, status=? WHERE cod_item=?");
    $stmt->bind_param("sssssssssssi", $nome, $categoria, $quantidade, $valor, $data_entrada, $descricao, $local, $origem, $foto_caminho, $documentos_json, $status, $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Item editado com sucesso!";
        header('Location: edit_item.php?id=' . $id);
        exit(0);
    }
}
?>