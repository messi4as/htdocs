<?php
session_start();
require 'db_connect.php';

////////////////////////////// CADASTRAR SÓCIOS E REPRESENTANTES /////////////////////////////////////////

if (isset($_POST['cad_emissor'])) {
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_emissor'])));
    $cpf = mysqli_real_escape_string($conn, trim($_POST['cpf_emissor']));
    $cnpj = mysqli_real_escape_string($conn, trim($_POST['cnpj_emissor']));
    $endereco = mysqli_real_escape_string($conn, trim($_POST['endereco_emissor']));
    $bairro = mysqli_real_escape_string($conn, trim($_POST['bairro_emissor']));

    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['documentos_emissores'])) {
        foreach ($_FILES['documentos_emissores']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_emissores']['error'][$key] == 0) {
                $documento_nome = $_FILES['documentos_emissor']['name'][$key] . '-' . uniqid();
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                if (move_uploaded_file($tmp_name, $documento_caminho)) {
                    $documentos[] = $documento_caminho;
                } else {
                    $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_emissores']['name'][$key];
                    header("Location: lista_emissor.php");
                    exit(0);
                }
            } else {
                $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_emissores']['name'][$key] . " - Código de erro: " . $_FILES['documentos_emissores']['error'][$key];
                header("Location: lista_emissor.php");
                exit(0);
            }
        }
    } else {
        $_SESSION['mensagem'] = "Nenhum arquivo foi enviado.";
        header("Location: lista_emissor.php");
        exit(0);
    }

    // Verificar se o array de documentos não está vazio
    if (empty($documentos)) {
        $_SESSION['mensagem'] = "Nenhum documento foi processado.";
        header("Location: lista_emissor.php");
        exit(0);
    }

    $documentos_json = json_encode($documentos);

    // Verificar se a codificação JSON foi bem-sucedida
    if ($documentos_json === false) {
        $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
        header("Location: lista_emissor.php");
        exit(0);
    }

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO emissor (nome_emissor, cpf_emissor, cnpj_emissor, endereco_emissor, bairro_emissor, documentos_emissor) 
    VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $nome, $cpf, $cnpj, $endereco, $bairro, $documentos_json);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Emissor Cadastrado com Sucesso";
        header('Location: lista_emissor.php');
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Emissor não Cadastrado: " . $stmt->error;
        header('Location: lista_emissor.php');
        exit(0);
    }
}
/////////////////////////// EDITAR SÓCIO E REPRESENTANTES  ////////////////////////////
if (isset($_POST['edit_emissor'])) {
    $id_emissor = $_POST['id_emissor'];
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_emissor'])));
    $cpf = mysqli_real_escape_string($conn, trim($_POST['cpf_emissor']));
    $cnpj = mysqli_real_escape_string($conn, trim($_POST['cnpj_emissor']));
    $endereco = mysqli_real_escape_string($conn, trim($_POST['endereco_emissor']));
    $bairro = mysqli_real_escape_string($conn, trim($_POST['bairro_emissor']));

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
        if (isset($_FILES['documentos_emissor']) && count($_FILES['documentos_emissor']['tmp_name']) > 0 && $_FILES['documentos_emissor']['error'][0] == 0) {
            foreach ($_FILES['documentos_emissor']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documentos_emissor']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['documentos_emissor']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['documentos_emissor']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_emissor']['name'][$key];
                        header('Location: edit_emissor.php?id=' . $id_emissor);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_emissor']['name'][$key] . " - Código de erro: " . $_FILES['documentos_emissor']['error'][$key];
                    header('Location: edit_emissor.php?id=' . $id_emissor);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os documentos atuais se não houver novos
        $documentos = json_decode($_POST['documentos_atuais'], true);
        if (isset($_FILES['documentos_emissor']) && count($_FILES['documentos_emissor']['tmp_name']) > 0 && $_FILES['documentos_emissor']['error'][0] == 0) {
            foreach ($_FILES['documentos_emissor']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documentos_emissor']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['documentos_emissor']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['documentos_emissor']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_emissor']['name'][$key];
                        header('Location: edit_emissor.php?id=' . $id_emissor);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_emissor']['name'][$key] . " - Código de erro: " . $_FILES['documentos_emissor']['error'][$key];
                    header('Location: edit_emissor.php?id=' . $id_emissor);
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
    $stmt = $conn->prepare("UPDATE emissor SET nome_emissor=?, cpf_emissor=?, cnpj_emissor=?, endereco_emissor=?, bairro_emissor=?, documentos_emissor=? WHERE cod_emissor =?");
    $stmt->bind_param("ssssssi", $nome, $cpf, $cnpj, $endereco, $bairro, $documentos_json, $id_emissor);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Sócio Editado com Sucesso';
        header('Location: edit_emissor.php?id=' . $id_emissor);
        exit(0);
    } else {
        $_SESSION['mensagem'] = 'Sócio não Editado';
        header('Location: edit_emissor.php?id=' . $id_emissor);
        exit(0);
    }
}

//////////////////////////////////// CADASTRAR PRESTADORES ///////////////////////////////////

if (isset($_POST['cad_emitente'])) {
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_emitente'])));
    $nacionalidade = (mysqli_real_escape_string($conn, trim($_POST['nacionalidade_emitente'])));
    $cpf = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf_emitente'])));
    $rg = strtoupper(mysqli_real_escape_string($conn, trim($_POST['rg_emitente'])));
    $estadocivil = (mysqli_real_escape_string($conn, trim($_POST['estado_civil_emitente'])));
    $nomeempresa = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_empresa'])));
    $cnpj = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cnpj_emitente'])));
    $endereco = (mysqli_real_escape_string($conn, trim($_POST['endereco_emitente'])));
    $bairro = (mysqli_real_escape_string($conn, trim($_POST['bairro_emitente'])));
    $cep = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cep_emitente'])));
    $banco = strtoupper(mysqli_real_escape_string($conn, trim($_POST['banco_emitente'])));
    $agencia = strtoupper(mysqli_real_escape_string($conn, trim($_POST['agencia_emitente'])));
    $conta = strtoupper(mysqli_real_escape_string($conn, trim($_POST['conta_emitente'])));
    $tipochave = strtoupper(mysqli_real_escape_string($conn, trim($_POST['tipo_chave_pix_emitente'])));
    $chavepix = strtoupper(mysqli_real_escape_string($conn, trim($_POST['chave_pix_emitente'])));
    $favorecido = strtoupper(mysqli_real_escape_string($conn, trim($_POST['favorecido_emitente'])));
    $cpffavorecido = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf_favorecido_emitente'])));

    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['documentos_emitentes'])) {
        foreach ($_FILES['documentos_emitentes']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_emitentes']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['documentos_emitentes']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                if (move_uploaded_file($tmp_name, $documento_caminho)) {
                    $documentos[] = $documento_caminho;
                } else {
                    $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_emitentes']['name'][$key];
                    header("Location: lista_emissor.php");
                    exit(0);
                }
            } else {
                $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_emitentes']['name'][$key] . " - Código de erro: " . $_FILES['documentos_emitentes']['error'][$key];
                header("Location: lista_emitente.php");
                exit(0);
            }
        }
    } else {
        $_SESSION['mensagem'] = "Nenhum arquivo foi enviado.";
        header("Location: lista_emitente.php");
        exit(0);
    }

    // Verificar se o array de documentos não está vazio
    if (empty($documentos)) {
        $_SESSION['mensagem'] = "Nenhum documento foi processado.";
        header("Location: lista_emitente.php");
        exit(0);
    }

    $documentos_json = json_encode($documentos);

    // Verificar se a codificação JSON foi bem-sucedida
    if ($documentos_json === false) {
        $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
        header("Location: lista_emitente.php");
        exit(0);
    }

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO emitente (nome_emitente, nacionalidade_emitente, cpf_emitente, rg_emitente, estado_civil_emitente, nome_empresa, 
    cnpj_emitente, endereco_emitente, bairro_emitente, cep_emitente, banco_emitente, agencia_emitente, conta_emitente, tipo_chave_pix_emitente,
    chave_pix_emitente, favorecido_emitente, cpf_favorecido_emitente, documentos_emitente) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ssssssssssssssssss",
        $nome,
        $nacionalidade,
        $cpf,
        $rg,
        $estadocivil,
        $nomeempresa,
        $cnpj,
        $endereco,
        $bairro,
        $cep,
        $banco,
        $agencia,
        $conta,
        $tipochave,
        $chavepix,
        $favorecido,
        $cpffavorecido,
        $documentos_json
    );


    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Emitente Cadastrado com Sucesso";
        header('Location: lista_emitente.php');
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Emitente não Cadastrado: " . $stmt->error;
        header('Location: lista_emitente.php');
        exit(0);
    }
}

//////////////////////////////////// EDITAR PRESTADORES //////////////////////////////

if (isset($_POST['edit_emitente'])) {
    $id_emitente = $_POST['id_emitente'];
    $nome = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_emitente'])));
    $nacionalidade = (mysqli_real_escape_string($conn, trim($_POST['nacionalidade_emitente'])));
    $cpf = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf_emitente'])));
    $rg = strtoupper(mysqli_real_escape_string($conn, trim($_POST['rg_emitente'])));
    $estadocivil = (mysqli_real_escape_string($conn, trim($_POST['estado_civil_emitente'])));
    $nomeempresa = strtoupper(mysqli_real_escape_string($conn, trim($_POST['nome_empresa'])));
    $cnpj = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cnpj_emitente'])));
    $endereco = (mysqli_real_escape_string($conn, trim($_POST['endereco_emitente'])));
    $bairro = (mysqli_real_escape_string($conn, trim($_POST['bairro_emitente'])));
    $cep = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cep_emitente'])));
    $banco = (mysqli_real_escape_string($conn, trim($_POST['banco_emitente'])));
    $agencia = strtoupper(mysqli_real_escape_string($conn, trim($_POST['agencia_emitente'])));
    $conta = strtoupper(mysqli_real_escape_string($conn, trim($_POST['conta_emitente'])));
    $tipochave = strtoupper(mysqli_real_escape_string($conn, trim($_POST['tipo_chave_pix_emitente'])));
    $chavepix = strtoupper(mysqli_real_escape_string($conn, trim($_POST['chave_pix_emitente'])));
    $favorecido = strtoupper(mysqli_real_escape_string($conn, trim($_POST['favorecido_emitente'])));
    $cpffavorecido = strtoupper(mysqli_real_escape_string($conn, trim($_POST['cpf_favorecido_emitente'])));


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
        if (isset($_FILES['documentos_emitente']) && count($_FILES['documentos_emitente']['tmp_name']) > 0 && $_FILES['documentos_emitente']['error'][0] == 0) {
            foreach ($_FILES['documentos_emitente']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documentos_emitente']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['documentos_emitente']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['documentos_emitente']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_emitente']['name'][$key];
                        header('Location: edit_emitente.php?id=' . $id_emitente);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_emitente']['name'][$key] . " - Código de erro: " . $_FILES['documentos_emitente']['error'][$key];
                    header('Location: edit_emitente.php?id=' . $id_emitente);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os documentos atuais se não houver novos
        $documentos = json_decode($_POST['documentos_atuais'], true);
        if (isset($_FILES['documentos_emitente']) && count($_FILES['documentos_emitente']['tmp_name']) > 0 && $_FILES['documentos_emitente']['error'][0] == 0) {
            foreach ($_FILES['documentos_emitente']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['documentos_emitente']['error'][$key] == 0) {
                    $nome_arquivo = pathinfo($_FILES['documentos_emitente']['name'][$key], PATHINFO_FILENAME);
                    $extensao_arquivo = pathinfo($_FILES['documentos_emitente']['name'][$key], PATHINFO_EXTENSION);
                    $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                    $documento_caminho = 'uploads/documentos/' . $documento_nome;
                    if (move_uploaded_file($tmp_name, $documento_caminho)) {
                        $documentos[] = $documento_caminho;
                    } else {
                        $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_emitente']['name'][$key];
                        header('Location: edit_emitente.php?id=' . $id_emitente);
                        exit(0);
                    }
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_emitente']['name'][$key] . " - Código de erro: " . $_FILES['documentos_emitente']['error'][$key];
                    header('Location: edit_emitente.php?id=' . $id_emitente);
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
        header("Location: lista_emitente.php");
        exit(0);
    }


    // Atualizar os dados no banco de dados
    $stmt = $conn->prepare("UPDATE emitente SET nome_emitente=?, nacionalidade_emitente=?, cpf_emitente=?, rg_emitente=?, estado_civil_emitente=?, nome_empresa=?, 
    cnpj_emitente=?, endereco_emitente=?, bairro_emitente=?, cep_emitente=?, banco_emitente=?, agencia_emitente=?, conta_emitente=?, tipo_chave_pix_emitente=?,
    chave_pix_emitente=?, favorecido_emitente=?, cpf_favorecido_emitente=?, documentos_emitente=? WHERE cod_emitente =?");
    $stmt->bind_param(
        "ssssssssssssssssssi",
        $nome,
        $nacionalidade,
        $cpf,
        $rg,
        $estadocivil,
        $nomeempresa,
        $cnpj,
        $endereco,
        $bairro,
        $cep,
        $banco,
        $agencia,
        $conta,
        $tipochave,
        $chavepix,
        $favorecido,
        $cpffavorecido,
        $documentos_json,
        $id_emitente
    );



    if ($stmt->execute()) {
        $_SESSION['mensagem'] = 'Prestador Editado com Sucesso';
        header('Location: edit_emitente.php?id=' . $id_emitente);
        exit(0);
    } else {
        $_SESSION['mensagem'] = 'Prestador não Editado';
        header('Location: edit_emitente.php?id=' . $id_emitente);
        exit(0);
    }
}

if (isset($_POST['cad_recibo'])) {
    $codemitente = (mysqli_real_escape_string($conn, trim($_POST['cod_emitente'])));
    $codemissor = (mysqli_real_escape_string($conn, trim($_POST['cod_emissor'])));
    $data = (mysqli_real_escape_string($conn, trim($_POST['data_recibo'])));
    $valor = (mysqli_real_escape_string($conn, trim($_POST['valor_recibo'])));
    $local = (mysqli_real_escape_string($conn, trim($_POST['local_recibo'])));
    $valorextrecibo = (mysqli_real_escape_string($conn, trim($_POST['valor_ext_recibo'])));
    $descricao = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['descricao_recibo'])));

    $sql = "INSERT INTO recibo (cod_emitente, cod_emissor, data_recibo, valor_recibo, local_recibo, valor_ext_recibo, descricao_recibo) 
            VALUES ('$codemitente', '$codemissor', '$data', '$valor', '$local','$valorextrecibo', '$descricao')";

    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Recibo Cadastrado com Sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Recibo não Cadastrado';
        header('Location: index.php');
        exit;
    }
}



if (isset($_POST['edit_recibo'])) {
    $id_recibo = mysqli_real_escape_string($conn, $_POST['id_recibo']);
    $codemitente = mysqli_real_escape_string($conn, trim($_POST['cod_emitente']));
    $codemissor = mysqli_real_escape_string($conn, trim($_POST['cod_emissor']));
    $data = mysqli_real_escape_string($conn, trim($_POST['data_recibo']));
    $valor = mysqli_real_escape_string($conn, trim($_POST['valor_recibo']));
    $local = mysqli_real_escape_string($conn, trim($_POST['local_recibo']));
    $valorextrecibo = mysqli_real_escape_string($conn, trim($_POST['valor_ext_recibo']));
    $descricao = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['descricao_recibo'])));

    $sql = "UPDATE recibo SET cod_emitente='$codemitente', cod_emissor='$codemissor', data_recibo='$data', valor_recibo='$valor', local_recibo='$local', valor_ext_recibo='$valorextrecibo', descricao_recibo='$descricao' WHERE cod_recibo = '$id_recibo'";

    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Recibo Editado com Sucesso';
        header('Location: view_recibo.php?id=' . $id_recibo);
        exit;
    } else {
        $_SESSION['mensagem'] = 'Recibo não Editado';
        header('Location: view_recibo.php?id=' . $id_recibo);
        exit;
    }
}

if (isset($_POST['delete_emissor'])) {
    $os_id = mysqli_real_escape_string($conn, $_POST['delete_emissor']);
    $sql = "DELETE FROM emissor WHERE cod_emissor = '$os_id'";
    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Emissor deletado com sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Emissor não foi deletado';
        header('Location: index.php');
        exit;
    }
}

if (isset($_POST['delete_emitente'])) {
    $os_id = mysqli_real_escape_string($conn, $_POST['delete_emitente']);
    $sql = "DELETE FROM emitente WHERE cod_emitente = '$os_id'";
    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Emitente deletado com sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Emitente não foi deletado';
        header('Location: index.php');
        exit;
    }
}

if (isset($_POST['delete_recibo'])) {
    $os_id = mysqli_real_escape_string($conn, $_POST['delete_recibo']);
    $sql = "DELETE FROM recibo WHERE cod_recibo = '$os_id'";
    mysqli_query($conn, $sql);

    if (mysqli_affected_rows($conn) > 0) {
        $_SESSION['mensagem'] = 'Recibo deletado com sucesso';
        header('Location: index.php');
        exit;
    } else {
        $_SESSION['mensagem'] = 'Recibo não foi deletado';
        header('Location: index.php');
        exit;
    }
}
