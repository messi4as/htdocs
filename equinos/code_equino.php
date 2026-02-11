<?php
session_start();
require '../db_connect.php';

// --- DEFINIR FOTO COMO CAPA ---
if (isset($_GET['definir_capa'])) {
    $id_foto = mysqli_real_escape_string($conn, $_GET['definir_capa']);
    $cod_equino = mysqli_real_escape_string($conn, $_GET['cod_equino']);

    // 1. Retira a capa de todas as fotos deste equino
    mysqli_query($conn, "UPDATE fotos_equinos SET capa = 0 WHERE cod_equino = '$cod_equino'");
    
    // 2. Define a foto selecionada como capa
    mysqli_query($conn, "UPDATE fotos_equinos SET capa = 1 WHERE id_foto = '$id_foto'");
    
    // 3. Atualiza o nome da foto na tabela principal para facilitar a listagem (opcional, mas recomendado)
    $res = mysqli_query($conn, "SELECT caminho FROM fotos_equinos WHERE id_foto = '$id_foto'");
    $foto = mysqli_fetch_array($res);
    $nome_foto = $foto['caminho'];
    mysqli_query($conn, "UPDATE equinos SET foto_capa = '$nome_foto' WHERE cod_equino = '$cod_equino'");

    $_SESSION['mensagem'] = "Foto de capa atualizada!";
    header("Location: view_equino.php?id=$cod_equino");
    exit(0);
}

// --- EXCLUIR FOTO ---
if (isset($_GET['excluir_foto'])) {
    $id_foto = mysqli_real_escape_string($conn, $_GET['excluir_foto']);
    $cod_equino = mysqli_real_escape_string($conn, $_GET['cod_equino']);

    // Busca o nome do ficheiro para apagar da pasta
    $res = mysqli_query($conn, "SELECT caminho, capa FROM fotos_equinos WHERE id_foto = '$id_foto'");
    $foto = mysqli_fetch_array($res);

    if ($foto) {
        $caminho_ficheiro = "uploads/imagens/" . $foto['caminho'];
        if (file_exists($caminho_ficheiro)) {
            unlink($caminho_ficheiro); // Apaga o ficheiro físico
        }
        
        mysqli_query($conn, "DELETE FROM fotos_equinos WHERE id_foto = '$id_foto'");

        // Se a foto apagada era a capa, limpa o campo na tabela principal
        if ($foto['capa'] == 1) {
            mysqli_query($conn, "UPDATE equinos SET foto_capa = '' WHERE cod_equino = '$cod_equino'");
        }
    }

    $_SESSION['mensagem'] = "Foto excluída com sucesso!";
    header("Location: view_equino.php?id=$cod_equino");
    exit(0);
}

// --- CADASTRAR/EDITAR EQUINO E UPLOAD DE MÚLTIPLAS FOTOS ---
if (isset($_POST['save_equino']) || isset($_POST['update_equino'])) {
    $nome_animal = mysqli_real_escape_string($conn, $_POST['nome_animal']);
    $proprietario = mysqli_real_escape_string($conn, $_POST['proprietario']);
    $num_registro = mysqli_real_escape_string($conn, $_POST['num_registro']);
    $raca = mysqli_real_escape_string($conn, $_POST['raca']);
    $pelagem = mysqli_real_escape_string($conn, $_POST['pelagem']);
    $sexo = mysqli_real_escape_string($conn, $_POST['sexo']);
    $data_nascimento = mysqli_real_escape_string($conn, $_POST['data_nascimento']);
    $local = mysqli_real_escape_string($conn, $_POST['local']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $descricao_geral = mysqli_real_escape_string($conn, $_POST['descricao_geral']);

    if (isset($_POST['save_equino'])) {
        $query = "INSERT INTO equinos (nome_animal, proprietario, num_registro, raca, pelagem, sexo, data_nascimento, local, status, descricao_geral) 
                  VALUES ('$nome_animal', '$proprietario', '$num_registro', '$raca', '$pelagem', '$sexo', '$data_nascimento', '$local', '$status', '$descricao_geral')";
        mysqli_query($conn, $query);
        $cod_equino = mysqli_insert_id($conn);
    } else {
        $cod_equino = mysqli_real_escape_string($conn, $_POST['cod_equino']);
        $query = "UPDATE equinos SET nome_animal='$nome_animal', proprietario='$proprietario', num_registro='$num_registro', raca='$raca', 
                  pelagem='$pelagem', sexo='$sexo', data_nascimento='$data_nascimento', local='$local', status='$status', descricao_geral='$descricao_geral' 
                  WHERE cod_equino='$cod_equino'";
        mysqli_query($conn, $query);
    }

    // LÓGICA DE UPLOAD MÚLTIPLO
    if (isset($_FILES['fotos']['name'][0]) && $_FILES['fotos']['name'][0] != "") {
        $diretorio = "uploads/imagens/";
        if (!is_dir($diretorio)) mkdir($diretorio, 0777, true);

        foreach ($_FILES['fotos']['name'] as $key => $val) {
            $extensao = pathinfo($_FILES['fotos']['name'][$key], PATHINFO_EXTENSION);
            $novo_nome = md5(time() . rand()) . "." . $extensao;

            if (move_uploaded_file($_FILES['fotos']['tmp_name'][$key], $diretorio . $novo_nome)) {
                // Insere na tabela de fotos
                mysqli_query($conn, "INSERT INTO fotos_equinos (cod_equino, caminho) VALUES ('$cod_equino', '$novo_nome')");
            }
        }
    }

    $_SESSION['mensagem'] = "Dados salvos com sucesso!";
    header("Location: view_equino.php?id=$cod_equino");
    exit(0);
}

// --- REGISTAR OCORRÊNCIA COM ANEXO ---
if (isset($_POST['save_ocorrencia'])) {
    $cod_equino = mysqli_real_escape_string($conn, $_POST['cod_equino']);
    $data_evento = mysqli_real_escape_string($conn, $_POST['data_evento']);
    $tipo_evento = mysqli_real_escape_string($conn, $_POST['tipo_evento']);
    $peso_kg = mysqli_real_escape_string($conn, $_POST['peso_kg']);
    $veterinario = mysqli_real_escape_string($conn, $_POST['veterinario']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao_detalhada']);
    
    $nome_anexo = null;

    // Lógica de Upload do Documento
    if (isset($_FILES['anexo']['name']) && $_FILES['anexo']['name'] != "") {
        $diretorio_doc = "uploads/documentos/";
        
        // Cria a pasta se não existir
        if (!is_dir($diretorio_doc)) {
            mkdir($diretorio_doc, 0777, true);
        }

        $extensao = pathinfo($_FILES['anexo']['name'], PATHINFO_EXTENSION);
        // Nome único para evitar ficheiros duplicados
        $nome_anexo = "DOC_" . md5(time() . rand()) . "." . $extensao;

        move_uploaded_file($_FILES['anexo']['tmp_name'], $diretorio_doc . $nome_anexo);
    }

    $query = "INSERT INTO ocorrencias_equinos (cod_equino, data_evento, tipo_evento, peso_kg, veterinario, descricao_detalhada, anexo) 
              VALUES ('$cod_equino', '$data_evento', '$tipo_evento', '$peso_kg', '$veterinario', '$descricao', '$nome_anexo')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['mensagem'] = "Ocorrência e documento registados!";
        header("Location: view_equino.php?id=$cod_equino");
        exit(0);
    }
}