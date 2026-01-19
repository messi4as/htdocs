<?php
session_start();
require 'db_connect.php';

// Função auxiliar para limpar e normalizar o HTML de entrada
function sanitize_html($html_input) {
    // 1. Normaliza tags semânticas e de parágrafo para tags simples (b, i, u, br)
    $clean_html = str_replace(
        // Adicionando <strike> e removendo <s> para limpeza completa
        array('<strong>', '</strong>', '<em>', '</em>', '<p>', '</p>', "\r\n", '<s>', '</s>', '<strike>', '</strike>'), 
        array('<b>', '</b>', '<i>', '</i>', '<br/>', '', '<br>', '', '', '', ''),
        $html_input
    );

    // 2. Remove tags <span> genéricas e lixo que pode atrapalhar o JasperReports
    $clean_html = preg_replace('/<span[^>]*>/i', '', $clean_html);
    $clean_html = str_replace('</span>', '', $clean_html);
    
    // Tenta remover apenas o caractere invisível do editor (o quebra-espaço zero-width)
    $clean_html = str_replace('﻿', '', $clean_html); 
    
    // 3. Remove quebras de linha duplicadas (deixando apenas uma)
    $clean_html = preg_replace('/<br\/>(\s*<br\/>)+/i', '<br/>', $clean_html); 
    
    // 4. CORREÇÃO: Remove o <br/> (quebra de linha) se ele estiver no início da string
    // Isso resolve o problema do <p> inicial gerado pelo editor.
    if (strpos($clean_html, '<br/>') === 0) {
        $clean_html = substr($clean_html, 5); // Remove os primeiros 5 caracteres: <br/>
    }
    
    // 5. Remove espaços no início e fim
    $clean_html = trim($clean_html); 

    return $clean_html;
}

if (isset($_POST['create_os'])) {
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome'])));
    $cpf = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf'])));
    $cnpj = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cnpj'])));
    $endereco = strtoupper(mysqli_real_escape_string($conn, trim($_POST['endereco'])));
    $cidade = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cidade'])));
    $celular = strtoupper(mysqli_real_escape_string($conn, trim($_POST['celular'])));
    $cep = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cep'])));
    $data = strtoupper(mysqli_real_escape_string($conn, trim($_POST['data'])));
    $valor = strtoupper(mysqli_real_escape_string($conn, trim($_POST['valor'])));
    $telefone_fixo = strtoupper(mysqli_real_escape_string($conn, trim($_POST['telefone_fixo'])));
    
    // Processamento da descrição e forma de pagamento
    $descricao_limpa = sanitize_html($_POST['descricao']);
    $descricao = mysqli_real_escape_string($conn, $descricao_limpa);
    
    $forma_pagamento_limpa = sanitize_html($_POST['forma_pagamento']);
    $forma_pagamento = mysqli_real_escape_string($conn, $forma_pagamento_limpa);
    
    // AVISO: O uso de strtoupper em campos HTML pode quebrar a estrutura.
    // Certifique-se de que a formatação não seja afetada se você usar strtoupper no campo que tem HTML.
    // Exemplo: $descricao = strtoupper($descricao);

    $sql = "INSERT INTO ordem_servico (nome, cpf, cnpj, endereco, cidade, celular, cep, data, descricao, valor, forma_pagamento, telefone_fixo) VALUES ('$nome', '$cpf', '$cnpj', '$endereco', '$cidade', '$celular', '$cep', '$data', '$descricao', '$valor', '$forma_pagamento', '$telefone_fixo')";

    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Ordem de Serviço Cadastrada com Sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Ordem de Serviço não Cadastrada';
        header('Location: index.php');
        exit;
    }
}

if (isset($_POST['edit_os'])) {
    $os_id = mysqli_real_escape_string($conn, $_POST['os_id']);
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome'])));
    $cpf = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf'])));
    $cnpj = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cnpj'])));
    $endereco = strtoupper(mysqli_real_escape_string($conn, trim($_POST['endereco'])));
    $cidade = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cidade'])));
    $celular = strtoupper(mysqli_real_escape_string($conn, trim($_POST['celular'])));
    $cep = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cep'])));
    $data = strtoupper(mysqli_real_escape_string($conn, trim($_POST['data'])));
    $valor = strtoupper(mysqli_real_escape_string($conn, trim($_POST['valor'])));
    $telefone_fixo = strtoupper(mysqli_real_escape_string($conn, trim($_POST['telefone_fixo'])));
    
    // Processamento da descrição e forma de pagamento
    $descricao_limpa = sanitize_html($_POST['descricao']);
    $descricao = mysqli_real_escape_string($conn, $descricao_limpa);
    
    $forma_pagamento_limpa = sanitize_html($_POST['forma_pagamento']);
    $forma_pagamento = mysqli_real_escape_string($conn, $forma_pagamento_limpa);

    // AVISO: O uso de strtoupper em campos HTML pode quebrar a estrutura.
    // Exemplo: $descricao = strtoupper($descricao);

    $sql = "UPDATE ordem_servico SET nome='$nome', cpf='$cpf', cnpj='$cnpj', endereco='$endereco', cidade='$cidade', celular='$celular', cep='$cep', data='$data', descricao='$descricao', valor='$valor', forma_pagamento='$forma_pagamento', telefone_fixo='$telefone_fixo' WHERE codigo = '$os_id'";

    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Ordem de Serviço Editada com Sucesso';
        header('Location: view_os.php?id=' . $os_id);
        exit;
    } else {
        $_SESSION['mensagem'] = 'Ordem de Serviço não Editada';
        header('Location: view_os.php?id=' . $os_id);
        exit;
    }
}

if (isset($_POST['delete_os'])) {
    $os_id = mysqli_real_escape_string($conn, $_POST['delete_os']);
    $sql = "DELETE FROM ordem_servico WHERE codigo = '$os_id'";
    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Ordem de Serviço deletada com sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Ordem de Serviço não foi deletada';
        header('Location: index.php');
        exit;
    }
}
?>