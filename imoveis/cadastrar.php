<?php
session_start();
require 'db_connect.php';

if (isset($_POST['cad_imoveis'])) {
    $nome = htmlspecialchars($_POST['nome_imovel']);
    $endereco  = htmlspecialchars($_POST['endereco_imovel']);
    $bairro  = htmlspecialchars($_POST['bairro_imovel']);
    $cep  = htmlspecialchars($_POST['cep_imovel']);
    $localizacao  = htmlspecialchars($_POST['localizacao_imovel']);
$proprietario_raw = $_POST['proprietario_imovel'];
$proprietario_limpo = str_replace(["\r\n", "\n"], "", $proprietario_raw); // Remove \r\n e \n
$proprietario = mysqli_real_escape_string($conn, $proprietario_limpo);    
$inscricao_raw = $_POST['inscricao_imovel'];
$inscricao_limpo = str_replace(["\r\n", "\n"], "", $inscricao_raw); // Remove \r\n e \n
$inscricao = mysqli_real_escape_string($conn, $inscricao_limpo);     
$condominio_raw = $_POST['condominio_imovel'];
$condominio_limpo = str_replace(["\r\n", "\n"], "", $condominio_raw); // Remove \r\n e \n
$condominio = mysqli_real_escape_string($conn, $condominio_limpo);       
$tv_raw = $_POST['tv_imovel'];
$tv_limpo = str_replace(["\r\n", "\n"], "", $tv_raw); // Remove \r\n e \n
$tv = mysqli_real_escape_string($conn, $tv_limpo);      
$energia_raw = $_POST['energia_imovel'];
$energia_limpo = str_replace(["\r\n", "\n"], "", $energia_raw); // Remove \r\n e \n
$energia = mysqli_real_escape_string($conn, $energia_limpo);       
$agua_raw = $_POST['agua_imovel'];
$agua_limpo = str_replace(["\r\n", "\n"], "", $agua_raw); // Remove \r\n e \n
$agua = mysqli_real_escape_string($conn, $agua_limpo);     
$gas_raw = $_POST['gas_imovel'];
$gas_limpo = str_replace(["\r\n", "\n"], "", $gas_raw); // Remove \r\n e \n
$gas = mysqli_real_escape_string($conn, $gas_limpo);       
$internet_raw = $_POST['internet_imovel'];
$internet_limpo = str_replace(["\r\n", "\n"], "", $internet_raw); // Remove \r\n e \n
$internet = mysqli_real_escape_string($conn, $internet_limpo);  



    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['documentos_imoveis'])) {
        foreach ($_FILES['documentos_imoveis']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_imoveis']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['documentos_imoveis']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                move_uploaded_file($tmp_name, $documento_caminho);
                $documentos[] = $documento_caminho;
            }
        }
    }
    $documentos_json = json_encode($documentos);

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO imoveis (nome_imovel, endereco_imovel, bairro_imovel, cep_imovel, localizacao_imovel, proprietario_imovel, inscricao_imovel, condominio_imovel,
     tv_imovel, energia_imovel, agua_imovel, gas_imovel, internet_imovel, documentos_imovel ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssssss", $nome, $endereco, $bairro, $cep, $localizacao, $proprietario, $inscricao, $condominio, $tv, $energia, $agua, $gas, $internet, $documentos_json);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Imóvel cadastrado com sucesso!";
        header("Location: lista_imoveis.php");
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar Imovel: " . $stmt->error;
        header("Location: lista_imoveis.php");
        exit(0);
    }
}

if (isset($_POST['edit_imoveis'])) {
    $id = $_POST['id'];
    $nome = htmlspecialchars($_POST['nome_imovel']);
    $endereco  = htmlspecialchars($_POST['endereco_imovel']);
    $bairro  = htmlspecialchars($_POST['bairro_imovel']);
    $cep  = htmlspecialchars($_POST['cep_imovel']);
    $localizacao  = htmlspecialchars($_POST['localizacao_imovel']);
$proprietario_raw = $_POST['proprietario_imovel'];
$proprietario_limpo = str_replace(["\r\n", "\n"], "", $proprietario_raw); // Remove \r\n e \n
$proprietario = mysqli_real_escape_string($conn, $proprietario_limpo);    
$inscricao_raw = $_POST['inscricao_imovel'];
$inscricao_limpo = str_replace(["\r\n", "\n"], "", $inscricao_raw); // Remove \r\n e \n
$inscricao = mysqli_real_escape_string($conn, $inscricao_limpo);     
$condominio_raw = $_POST['condominio_imovel'];
$condominio_limpo = str_replace(["\r\n", "\n"], "", $condominio_raw); // Remove \r\n e \n
$condominio = mysqli_real_escape_string($conn, $condominio_limpo);       
$tv_raw = $_POST['tv_imovel'];
$tv_limpo = str_replace(["\r\n", "\n"], "", $tv_raw); // Remove \r\n e \n
$tv = mysqli_real_escape_string($conn, $tv_limpo);      
$energia_raw = $_POST['energia_imovel'];
$energia_limpo = str_replace(["\r\n", "\n"], "", $energia_raw); // Remove \r\n e \n
$energia = mysqli_real_escape_string($conn, $energia_limpo);       
$agua_raw = $_POST['agua_imovel'];
$agua_limpo = str_replace(["\r\n", "\n"], "", $agua_raw); // Remove \r\n e \n
$agua = mysqli_real_escape_string($conn, $agua_limpo);     
$gas_raw = $_POST['gas_imovel'];
$gas_limpo = str_replace(["\r\n", "\n"], "", $gas_raw); // Remove \r\n e \n
$gas = mysqli_real_escape_string($conn, $gas_limpo);       
$internet_raw = $_POST['internet_imovel'];
$internet_limpo = str_replace(["\r\n", "\n"], "", $internet_raw); // Remove \r\n e \n
$internet = mysqli_real_escape_string($conn, $internet_limpo);  
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
        if (isset($_FILES['documentos_imovel']) && count($_FILES['documentos_imovel']['tmp_name']) > 0 && $_FILES['documentos_imovel']['error'][0] == 0) {
            foreach ($_FILES['documentos_imovel']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documentos_imovel']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['documentos_imovel']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['documentos_imovel']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_imovel']['name'][$key];
                        header('Location: edit_imoveis.php?id=' . $id);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_imovel']['name'][$key] . " - Código de erro: " . $_FILES['documentos_imovel']['error'][$key];
                    header('Location: edit_imoveis.php?id=' . $id);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os documentos atuais se não houver novos
        $documentos = json_decode($_POST['documentos_atuais'], true);
        if (isset($_FILES['documentos_imovel']) && count($_FILES['documentos_imovel']['tmp_name']) > 0 && $_FILES['documentos_imovel']['error'][0] == 0) {
            foreach ($_FILES['documentos_imovel']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documentos_imovel']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['documentos_imovel']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['documentos_imovel']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_imovel']['name'][$key];
                        header('Location: edit_imoveis.php?id=' . $id);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_imovel']['name'][$key] . " - Código de erro: " . $_FILES['documentos_imovel']['error'][$key];
                    header('Location: edit_imoveis.php?id=' . $id);
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
        header('Location: edit_emissor.php?id=' . $id);
        exit(0);
    }

    // Atualizar os dados no banco de dados
    $stmt = $conn->prepare("UPDATE imoveis SET nome_imovel=?, endereco_imovel=?, bairro_imovel=?, cep_imovel=?, localizacao_imovel=?, proprietario_imovel=?, inscricao_imovel=?, condominio_imovel=?,
     tv_imovel=?, energia_imovel=?, agua_imovel=?, gas_imovel=?, internet_imovel=?, documentos_imovel=?   WHERE cod_imovel=?");
    $stmt->bind_param("ssssssssssssssi", $nome, $endereco, $bairro, $cep, $localizacao, $proprietario, $inscricao, $condominio, $tv, $energia, $agua, $gas, $internet, $documentos_json, $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Imóvel editado com sucesso!";
        header('Location: edit_imoveis.php?id=' . $id);
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao editar Imóvel: " . $stmt->error;
        header('Location: edit_imoveis.php?id=' . $id);
        exit(0);
    }
}
