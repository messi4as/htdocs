<?php
session_start();
require 'db_connect.php';


if (isset($_POST['cad_veiculos'])) {
    $nome = htmlspecialchars($_POST['nome_veiculo']);
    $placa = htmlspecialchars($_POST['placa_veiculo']);
    $renavan = htmlspecialchars($_POST['renavan_veiculo']);
    $uf = htmlspecialchars($_POST['uf_veiculo']);
    $chassi = htmlspecialchars($_POST['chassi_veiculo']);
    $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
    $proprietario = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['propietario_veiculo']));

    // Processar a foto
    if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
        $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = null;
    }

    // Processar os documentos
    $documentos = [];
    if (isset($_FILES['documentos_veiculos'])) {
        foreach ($_FILES['documentos_veiculos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_veiculos']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['documentos_veiculos']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                move_uploaded_file($tmp_name, $documento_caminho);
                $documentos[] = $documento_caminho;
            }
        }
    }
    $documentos_json = json_encode($documentos);

    // Conectar ao banco de dados e inserir os dados
    $stmt = $conn->prepare("INSERT INTO veiculos (nome_veiculo, placa_veiculo, renavan_veiculo, uf_veiculo, chassi_veiculo, marca_modelo_veiculo, proprietario_veiculo, foto_veiculo, documentos_veiculo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Veículo cadastrado com sucesso!";
        header("Location: lista_veiculos.php");
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao cadastrar veículo: " . $stmt->error;
        header("Location: lista_veiculos.php");
        exit(0);
    }
}

if (isset($_POST['edit_veiculos'])) {
    $id = $_POST['id'];
    $nome = htmlspecialchars($_POST['nome_veiculo']);
    $placa = htmlspecialchars($_POST['placa_veiculo']);
    $renavan = htmlspecialchars($_POST['renavan_veiculo']);
    $uf = htmlspecialchars($_POST['uf_veiculo']);
    $chassi = htmlspecialchars($_POST['chassi_veiculo']);
    $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
    $proprietario = str_replace("\r\n", "<br>", (mysqli_real_escape_string($conn, $_POST['proprietario_veiculo'])));

  

    // Processar a foto
    if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
        // Excluir a foto antiga
        if (file_exists($_POST['foto_atual'])) {
            unlink($_POST['foto_atual']);
        }
        $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho);
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
        if (isset($_FILES['documentos_veiculo']) && count($_FILES['documentos_veiculo']['tmp_name']) > 0 && $_FILES['documentos_veiculo']['error'][0] == 0) {
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
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_veiculo']['name'][$key] . " - Código de erro: " . $_FILES['documentos_veiculo']['error'][$key];
                    header('Location: edit_veiculos.php?id=' . $id);
                    exit(0);
                }
            }
        }
    } else {
        // Manter os documentos atuais se não houver novos
        $documentos = json_decode($_POST['documentos_atuais'], true);
        if (isset($_FILES['documentos_veiculo']) && count($_FILES['documentos_veiculo']['tmp_name']) > 0 && $_FILES['documentos_veiculo']['error'][0] == 0) {
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
                } else {
                    $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_veiculo']['name'][$key] . " - Código de erro: " . $_FILES['documentos_veiculo']['error'][$key];
                    header('Location: edit_veiculos.php?id=' . $id);
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

  

    // Atualizar os dados no banco de dados
    $stmt = $conn->prepare("UPDATE veiculos SET nome_veiculo=?, placa_veiculo=?, renavan_veiculo=?, uf_veiculo=?, chassi_veiculo=?, marca_modelo_veiculo=?, proprietario_veiculo=?, foto_veiculo=?, documentos_veiculo=? WHERE cod_veiculo=?");
    $stmt->bind_param("sssssssssi", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json, $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Veículo editado com sucesso!";
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao editar veículo: " . $stmt->error;
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
}
