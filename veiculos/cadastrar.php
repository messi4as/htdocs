<?php
session_start();
require 'db_connect.php';

// --- BLOCO DE CADASTRO NOVO ---
if (isset($_POST['cad_veiculos'])) {
    $nome = htmlspecialchars($_POST['nome_veiculo']);
    $placa = htmlspecialchars($_POST['placa_veiculo']);
    $renavan = htmlspecialchars($_POST['renavan_veiculo']);
    $uf = htmlspecialchars($_POST['uf_veiculo']);
    $chassi = htmlspecialchars($_POST['chassi_veiculo']);
    $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
    $proprietario = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['propietario_veiculo']));

    if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
        $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = null;
    }

    // Processar os documentos com nomes personalizados
    $documentos = [];
    $nomes_custom = $_POST['nomes_arquivos'] ?? []; // Captura os nomes do formulário

    if (isset($_FILES['documentos_veiculos'])) {
        foreach ($_FILES['documentos_veiculos']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_veiculos']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['documentos_veiculos']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                
                if (move_uploaded_file($tmp_name, $documento_caminho)) {
                    // Guarda o par Nome e Caminho
                    $documentos[] = [
                        'nome' => htmlspecialchars($nomes_custom[$key] ?? 'Documento'),
                        'path' => $documento_caminho
                    ];
                }
            }
        }
    }
    $documentos_json = json_encode($documentos);

    $stmt = $conn->prepare("INSERT INTO veiculos (nome_veiculo, placa_veiculo, renavan_veiculo, uf_veiculo, chassi_veiculo, marca_modelo_veiculo, proprietario_veiculo, foto_veiculo, documentos_veiculo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Veículo cadastrado com sucesso!";
        header("Location: lista_veiculos.php");
        exit(0);
    }
}

// --- BLOCO DE EDIÇÃO ---
if (isset($_POST['edit_veiculos'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $nome = htmlspecialchars($_POST['nome_veiculo']);
    $placa = htmlspecialchars($_POST['placa_veiculo']);
    $renavan = htmlspecialchars($_POST['renavan_veiculo']);
    $uf = htmlspecialchars($_POST['uf_veiculo']);
    $chassi = htmlspecialchars($_POST['chassi_veiculo']);
    $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
    
    $proprietario_raw = $_POST['proprietario_veiculo'];
    $proprietario_limpo = str_replace(["\r\n", "\n"], "", $proprietario_raw);
    $proprietario = mysqli_real_escape_string($conn, $proprietario_limpo);

    // Foto
    if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
        if (file_exists($_POST['foto_atual'])) { unlink($_POST['foto_atual']); }
        $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho);
    } else {
        $foto_caminho = $_POST['foto_atual'];
    }

    // Lógica de Documentos na Edição
    $documentos = json_decode($_POST['documentos_atuais'], true) ?: [];
    $nomes_novos = $_POST['nomes_arquivos'] ?? []; // Nomes para os NOVOS ficheiros

    if (isset($_FILES['documentos_veiculo']) && $_FILES['documentos_veiculo']['error'][0] == 0) {
        foreach ($_FILES['documentos_veiculo']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_veiculo']['error'][$key] == 0) {
                $ext = pathinfo($_FILES['documentos_veiculo']['name'][$key], PATHINFO_EXTENSION);
                $doc_nome_final = uniqid() . '.' . $ext;
                $doc_caminho = 'uploads/documentos/' . $doc_nome_final;

                if (move_uploaded_file($tmp_name, $doc_caminho)) {
                    // Adiciona o novo documento ao array existente
                    $documentos[] = [
                        'nome' => htmlspecialchars($nomes_novos[$key] ?? 'Documento'),
                        'path' => $doc_caminho
                    ];
                }
            }
        }
    }

    $documentos_json = json_encode($documentos);

    $stmt = $conn->prepare("UPDATE veiculos SET nome_veiculo=?, placa_veiculo=?, renavan_veiculo=?, uf_veiculo=?, chassi_veiculo=?, marca_modelo_veiculo=?, proprietario_veiculo=?, foto_veiculo=?, documentos_veiculo=? WHERE cod_veiculo=?");
    $stmt->bind_param("sssssssssi", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json, $id);

    if ($stmt->execute()) {
        $_SESSION['mensagem'] = "Veículo editado com sucesso!";
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    } else {
        $_SESSION['mensagem'] = "Erro ao editar: " . $stmt->error;
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
}