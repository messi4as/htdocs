<?php
session_start();
require 'db_connect.php';

if (isset($_POST['create_os'])) {
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome'])));
    $cpf = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf'])));
    $cnpj = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cnpj'])));
    $endereco = strtoupper(mysqli_real_escape_string($conn, trim($_POST['endereco'])));
    $cidade = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cidade'])));
    $celular = strtoupper(mysqli_real_escape_string($conn, trim($_POST['celular'])));
    $cep = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cep'])));
    $data = strtoupper(mysqli_real_escape_string($conn, trim($_POST['data'])));
    $descricao = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['descricao'])));
    $valor = strtoupper(mysqli_real_escape_string($conn, trim($_POST['valor'])));
    $forma_pagamento = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['forma_pagamento'])));
    $telefone_fixo = strtoupper(mysqli_real_escape_string($conn, trim($_POST['telefone_fixo'])));

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
    $descricao = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['descricao']));
    $valor = strtoupper(mysqli_real_escape_string($conn, trim($_POST['valor'])));
    $forma_pagamento = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['forma_pagamento']));
    $telefone_fixo = strtoupper(mysqli_real_escape_string($conn, trim($_POST['telefone_fixo'])));

   

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
