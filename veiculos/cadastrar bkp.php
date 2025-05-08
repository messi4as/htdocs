<?php
session_start();
require 'db_connect.php';

// Verifique se a autenticação foi bem-sucedida
if (isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true) {
    echo "Autenticação bem-sucedida.<br>";
    if (isset($_POST['edit_veiculos'])) {
        echo "Dados do formulário recebidos.<br>";
        $id = $_POST['id'];
        $nome = htmlspecialchars($_POST['nome_veiculo']);
        $placa = htmlspecialchars($_POST['placa_veiculo']);
        $renavan = htmlspecialchars($_POST['renavan_veiculo']);
        $uf = htmlspecialchars($_POST['uf_veiculo']);
        $chassi = htmlspecialchars($_POST['chassi_veiculo']);
        $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
        $proprietario = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['proprietario_veiculo']));

        echo "Processando foto.<br>";
        // Processar a foto
        if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
            echo "Nova foto enviada.<br>";
            if (file_exists($_POST['foto_atual'])) {
                unlink($_POST['foto_atual']);
            }
            $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
            $foto_caminho = 'uploads/fotos/' . $foto_nome;
            move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho);
        } else {
            echo "Mantendo a foto atual.<br>";
            $foto_caminho = $_POST['foto_atual'];
        }

        echo "Processando documentos.<br>";
        // Processar os documentos
        $documentos = [];
        if (isset($_POST['acao_documentos']) && $_POST['acao_documentos'] == 'substituir') {
            echo "Substituindo documentos antigos.<br>";
            $documentos_atuais = json_decode($_POST['documentos_atuais'], true);
            if (!empty($documentos_atuais) && is_array($documentos_atuais)) {
                foreach ($documentos_atuais as $documento_atual) {
                    if (file_exists($documento_atual)) {
                        unlink($documento_atual);
                    }
                }
            }
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
            echo "Mantendo documentos atuais.<br>";
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

        echo "Verificando erros JSON.<br>";
        if (json_last_error() !== JSON_ERROR_NONE) {
            $documentos = [];
        }

        $documentos_json = json_encode($documentos);
        if ($documentos_json === false) {
            $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
            header('Location: edit_emissor.php?id=' . $id_emissor);
            exit(0);
        }

        echo "Obtendo dados antigos para auditoria.<br>";
        $stmt_old = $conn->prepare("SELECT * FROM veiculos WHERE cod_veiculo = ?");
        $stmt_old->bind_param("i", $id);
        $stmt_old->execute();
        $result_old = $stmt_old->get_result();
        $dados_antigos = $result_old->fetch_assoc();
        $dados_antigos_json = json_encode($dados_antigos);

        echo "Atualizando dados no banco de dados.<br>";
        $stmt = $conn->prepare("UPDATE veiculos SET nome_veiculo=?, placa_veiculo=?, renavan_veiculo=?, uf_veiculo=?, chassi_veiculo=?, marca_modelo_veiculo=?, proprietario_veiculo=?, foto_veiculo=?, documentos_veiculo=? WHERE cod_veiculo=?");
        $stmt->bind_param("sssssssssi", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json, $id);
        if ($stmt->execute()) {
            echo "Veículo editado com sucesso.<br>";
            $id_usuario = $_SESSION['id_usuario'];
            $operacao = "UPDATE";
            $nome_tabela = "veiculos";
            $dados_novos = [
                'nome_veiculo' => $nome,
                'placa_veiculo' => $placa,
                'renavan_veiculo' => $renavan,
                'uf_veiculo' => $uf,
                'chassi_veiculo' => $chassi,
                'marca_modelo_veiculo' => $marca_modelo,
                'proprietario_veiculo' => $proprietario,
                'foto_veiculo' => $foto_caminho,
                'documentos_veiculo' => $documentos_json
            ];
            $dados_novos_json = json_encode($dados_novos);

            echo "Salvando auditoria.<br>";
            $stmt_audit = $conn->prepare("INSERT INTO log_auditoria (id_usuario, operacao, nome_tabela, dados_antigos, dados_novos) VALUES (?, ?, ?, ?, ?)");
            $stmt_audit->bind_param("issss", $id_usuario, $operacao, $nome_tabela, $dados_antigos_json, $dados_novos_json);
            $stmt_audit->execute();

            $_SESSION['mensagem'] = "Veículo editado com sucesso!";
            header('Location: edit_veiculos.php?id=' . $id);
            exit(0);
        } else {
            $_SESSION['mensagem'] = "Erro ao editar veículo: " . $stmt->error;
            header('Location: edit_veiculos.php?id=' . $id);
            exit(0);
        }
    }
} else {
    $_SESSION['mensagem'] = "Autenticação necessária.";
    header('Location: lista_veiculos.php');
    exit(0);
}

$conn->close();