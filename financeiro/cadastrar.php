<?php
session_start();
require 'db_connect.php';

if (isset($_POST['cad_pagamentos'])) {
    $data = mysqli_real_escape_string($conn, trim($_POST['data']));
    $responsavel = mysqli_real_escape_string($conn, $_POST['responsavel']);
    $descricao = strtoupper(mysqli_real_escape_string($conn, $_POST['descricao']));
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $valor = mysqli_real_escape_string($conn, trim($_POST['valor']));
    $forma_pagamento = strtoupper(mysqli_real_escape_string($conn, $_POST['forma_pagamento']));



    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['documentos_pagamentos'])) {
        foreach ($_FILES['documentos_pagamentos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_pagamentos']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['documentos_pagamentos']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                move_uploaded_file($tmp_name, $documento_caminho);
                $documentos[] = $documento_caminho;
            }
        }
    }
    $documentos_json = json_encode($documentos);



    // Processar os comprovantes
    $comprovantes = [];
    if (isset($_FILES['comprovantes_pagamentos'])) {
        foreach ($_FILES['comprovantes_pagamentos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['comprovantes_pagamentos']['error'][$key] == 0) {
                $comprovante_nome = uniqid() . '-' . $_FILES['comprovantes_pagamentos']['name'][$key];
                $comprovante_caminho = 'uploads/comprovantes/' . $comprovante_nome;
                move_uploaded_file($tmp_name, $comprovante_caminho);
                $comprovantes[] = $comprovante_caminho;
            }
        }
    }
    $comprovantes_json = json_encode($comprovantes);

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO financeiro (data, responsavel, descricao, tipo, valor, forma_pagamento, documento, comprovante ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $data, $responsavel, $descricao, $tipo, $valor, $forma_pagamento, $documentos_json, $comprovantes_json);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Pagamento cadastrado com sucesso!";
        header("Location: index.php");
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar Pagamento: " . $stmt->error;
        header("Location: index.php");
        exit(0);
    }
}

if (isset($_POST['edit_pagamentos'])) {
    $id_financeiro = $_POST['id'];
    $data = mysqli_real_escape_string($conn, trim($_POST['data']));
    $responsavel = mysqli_real_escape_string($conn, $_POST['responsavel']);
    $descricao = strtoupper(mysqli_real_escape_string($conn, $_POST['descricao']));
    $tipo = mysqli_real_escape_string($conn, $_POST['tipo']);
    $valor = mysqli_real_escape_string($conn, trim($_POST['valor']));
    $forma_pagamento = strtoupper(mysqli_real_escape_string($conn, $_POST['forma_pagamento']));
    
    $forma_pagamento = strtoupper(mysqli_real_escape_string($conn, $_POST['forma_pagamento']));
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
         if (isset($_FILES['documentos_pagamentos']) && count($_FILES['documentos_pagamentos']['tmp_name']) > 0 && $_FILES['documentos_pagamentos']['error'][0] == 0) {
             foreach ($_FILES['documentos_pagamentos']['tmp_name'] as $key => $tmp_name) {
                 if ($_FILES['documentos_pagamentos']['error'][$key] == 0) {
                     $nome_arquivo = pathinfo($_FILES['documentos_pagamentos']['name'][$key], PATHINFO_FILENAME);
                     $extensao_arquivo = pathinfo($_FILES['documentos_pagamentos']['name'][$key], PATHINFO_EXTENSION);
                     $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                     $documento_caminho = 'uploads/documentos/' . $documento_nome;
                     if (move_uploaded_file($tmp_name, $documento_caminho)) {
                         $documentos[] = $documento_caminho;
                     } else {
                         $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_pagamentos']['name'][$key];
                         header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                         exit(0);
                     }
                 } else {
                     $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_pagamentos']['name'][$key] . " - Código de erro: " . $_FILES['documentos_pagamentos']['error'][$key];
                     header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                     exit(0);
                 }
             }
         }
     } else {
         // Manter os documentos atuais se não houver novos
         $documentos = json_decode($_POST['documentos_atuais'], true);
         if (isset($_FILES['documentos_pagamentos']) && count($_FILES['documentos_pagamentos']['tmp_name']) > 0 && $_FILES['documentos_pagamentos']['error'][0] == 0) {
             foreach ($_FILES['documentos_pagamentos']['tmp_name'] as $key => $tmp_name) {
                 if ($_FILES['documentos_pagamentos']['error'][$key] == 0) {
                     $nome_arquivo = pathinfo($_FILES['documentos_pagamentos']['name'][$key], PATHINFO_FILENAME);
                     $extensao_arquivo = pathinfo($_FILES['documentos_pagamentos']['name'][$key], PATHINFO_EXTENSION);
                     $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                     $documento_caminho = 'uploads/documentos/' . $documento_nome;
                     if (move_uploaded_file($tmp_name, $documento_caminho)) {
                         $documentos[] = $documento_caminho;
                     } else {
                         $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_pagamentos']['name'][$key];
                         header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                         exit(0);
                     }
                 } else {
                     $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_pagamentos']['name'][$key] . " - Código de erro: " . $_FILES['documentos_pagamentos']['error'][$key];
                     header('Location: edit_pagamentos.php?id=' . $id_financeiro);
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
         header('Location: edit_pagamentos.php?id=' . $id_financeiro);
         exit(0);
     }

    // Processar os comprovantes
    $comprovantes = [];
    if (isset($_POST['acao_comprovantes']) && $_POST['acao_comprovantes'] == 'substituir') {
        // Excluir os comprovantes antigos
        $comprovantes_atuais = json_decode($_POST['comprovantes_atuais'], true);
        if (!empty($comprovantes_atuais) && is_array($comprovantes_atuais)) {
            foreach ($comprovantes_atuais as $comprovante_atual) {
                if (file_exists($comprovante_atual)) {
                    unlink($comprovante_atual);
                }
            }
        }
        // Adicionar novos comprovantes
        if (isset($_FILES['comprovantes_pagamentos']) && count($_FILES['comprovantes_pagamentos']['tmp_name']) > 0 && $_FILES['comprovantes_pagamentos']['error'][0] == 0) {
            foreach ($_FILES['comprovantes_pagamentos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['comprovantes_pagamentos']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['comprovantes_pagamentos']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['comprovantes_pagamentos']['name'][$key], PATHINFO_EXTENSION);
                    $comprovante_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $comprovante_caminho = 'uploads/comprovantes/' . $comprovante_nome;
                    if (move_uploaded_file($tmp_name, $comprovante_caminho)) {
                        $comprovantes[] = $comprovante_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['comprovantes_pagamentos']['name'][$key];
                        header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['comprovantes_pagamentos']['name'][$key] . " - Código de erro: " . $_FILES['comprovantes_pagamentos']['error'][$key];
                    header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os comprovantes atuais se não houver novos
        $comprovantes = json_decode($_POST['comprovantes_atuais'], true);
        if (isset($_FILES['comprovantes_pagamentos']) && count($_FILES['comprovantes_pagamentos']['tmp_name']) > 0 && $_FILES['comprovantes_pagamentos']['error'][0] == 0) {
            foreach ($_FILES['comprovantes_pagamentos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['comprovantes_pagamentos']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['comprovantes_pagamentos']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['comprovantes_pagamentos']['name'][$key], PATHINFO_EXTENSION);
                    $comprovante_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $comprovante_caminho = 'uploads/comprovantes/' . $comprovante_nome;
                    if (move_uploaded_file($tmp_name, $comprovante_caminho)) {
                        $comprovantes[] = $comprovante_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['comprovantes_pagamentos']['name'][$key];
                        header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['comprovantes_pagamentos']['name'][$key] . " - Código de erro: " . $_FILES['comprovantes_pagamentos']['error'][$key];
                    header('Location: edit_pagamentos.php?id=' . $id_financeiro);
                    exit(0);
                }
            }
        }
    }

    // Verificar se a decodificação JSON foi bem-sucedida
    if (json_last_error() !== JSON_ERROR_NONE) {
        $comprovantes = [];
    }

    $comprovantes_json = json_encode($comprovantes);

    // Verificar se a codificação JSON foi bem-sucedida
    if ($comprovantes_json === false) {
        $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
        header('Location: edit_pagamentos.php?id=' . $id_financeiro);
        exit(0);
    }


    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("UPDATE financeiro SET data=?, responsavel=?, descricao=?, tipo=?, valor=?, forma_pagamento=?, documento=?, comprovante=?
    WHERE cod_financeiro=?");
    $stmt->bind_param("ssssssssi", $data, $responsavel, $descricao, $tipo, $valor, $forma_pagamento, $documentos_json, $comprovantes_json, $id_financeiro);


    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Pagamento editado com sucesso!";
        header('Location: edit_pagamentos.php?id=' . $id_financeiro);
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao editar Pagamento: " . $stmt->error;
        header('Location: edit_pagamentos.php?id=' . $id_financeiro);
        exit(0);
    }
}
