<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validação e limpeza dos dados de entrada
    $nome_cartao = htmlspecialchars($_POST['nome_cartao']);
    $data_compra = htmlspecialchars($_POST['data_compra']);
    $quantidade_parcelas = intval(htmlspecialchars($_POST['quantidade_parcelas']));
    $valor_total_texto = htmlspecialchars($_POST['valor']);
    $valor_total = str_replace(['R$ ', '.', ','], ['', '', '.'], $valor_total_texto); // Remove formatação e converte para float
    $descricao = strtoupper(htmlspecialchars($_POST['descricao']));
    $primeira_vencimento = htmlspecialchars($_POST['primeira_vencimento']);
    $percentual_responsavel1 = floatval(htmlspecialchars($_POST['percentual_responsavel1'])) / 100;
    $percentual_responsavel2 = floatval(htmlspecialchars($_POST['percentual_responsavel2'])) / 100;

    // Validação do valor total
    if (!is_numeric($valor_total)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Valor total inválido.']);
        exit();
    }

    // Validação da quantidade de parcelas
    if ($quantidade_parcelas <= 0) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Quantidade de parcelas inválida.']);
        exit();
    }

    // Validação das datas
    if (empty($data_compra) || empty($primeira_vencimento)) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Data da compra e primeira data de vencimento são obrigatórias.']);
        exit();
    }

    // Validação dos percentuais
    if ($percentual_responsavel1 + $percentual_responsavel2 != 1) {
        echo json_encode(['sucesso' => false, 'mensagem' => 'A soma dos percentuais deve ser igual a 100%.']);
        exit();
    }

    $documentos_nomes = [];
    if (isset($_FILES['anexo']) && is_array($_FILES['anexo']['name'])) {
        foreach ($_FILES['anexo']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['anexo']['error'][$key] == 0) {
                $documento_nome = uniqid() . '-' . $_FILES['anexo']['name'][$key];
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                if (move_uploaded_file($tmp_name, $documento_caminho)) {
                    $documentos_nomes[] = $documento_caminho;
                } else {
                    echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao fazer o upload de um dos arquivos.']);
                    exit();
                }
            } else if ($_FILES['anexo']['error'][$key] != 4) { // Adicionado para tratar erros de upload
                echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao fazer o upload de um dos arquivos. Erro código: ' . $_FILES['anexo']['error'][$key]]);
                exit();
            }
        }
    }
    $anexo_json = json_encode($documentos_nomes);

    // Inserir a compra na tabela 'compras'
    $stmt_compra = $conn->prepare("INSERT INTO compras (nome_cartao, data_compra, quantidade_parcelas, valor, descricao, anexo) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_compra->bind_param("sssdss", $nome_cartao, $data_compra, $quantidade_parcelas, $valor_total, $descricao, $anexo_json);

    if ($stmt_compra->execute()) {
        $id_compra = $conn->insert_id;

        // Calcular e inserir os parcelamentos com ajuste na primeira parcela
        $valor_total_numerico = floatval($valor_total); // Garante que o valor total seja numérico
        $valor_parcela_exato = $valor_total / $quantidade_parcelas;
        $valor_parcela_padrao = round($valor_parcela_exato, 2);
        $soma_parcelas_padrao = ($quantidade_parcelas - 1) * $valor_parcela_padrao;
        $valor_primeira_parcela = round($valor_total - $soma_parcelas_padrao, 2);
        $data_vencimento = new DateTime($primeira_vencimento);

        for ($i = 1; $i <= $quantidade_parcelas; $i++) {
            $referencia_parcela = $i . " de " . $quantidade_parcelas;
            $valor_parcela_atual = ($i === 1) ? $valor_primeira_parcela : $valor_parcela_padrao;
            $valor_resp1 = round($valor_parcela_atual * $percentual_responsavel1, 2);
            $valor_resp2 = round($valor_parcela_atual * $percentual_responsavel2, 2);
            $data_vencimento_formatada = $data_vencimento->format('Y-m-d');

            // Calcular o valor exato para o Responsável 1 primeiro
            $valor_resp1_exato = $valor_parcela_atual * $percentual_responsavel1;
            $valor_resp1 = round($valor_resp1_exato, 2);

            // Calcular o valor do Responsável 2 subtraindo do total da parcela
            $valor_resp2 = round($valor_parcela_atual - $valor_resp1, 2);


            // Ajuste para a primeira parcela em caso de centavos ímpares
            if ($i === 1) {
                $total_parcela_primeira = $valor_resp1 + $valor_resp2;
                $diferenca = round($valor_primeira_parcela - $total_parcela_primeira, 2);

                if (abs($diferenca) === 0.01 && fmod($valor_total_numerico * 100, 2) !== 0) {
                    $valor_resp1 += $diferenca;
                    $valor_resp2 = round($valor_primeira_parcela - $valor_resp1, 2);
                }
            }

            $stmt_parcela = $conn->prepare("INSERT INTO parcelamentos (id_compra, data_vencimento, referencia_parcela, valor_parcela_responsavel1, valor_parcela_responsavel2) VALUES (?, ?, ?, ?, ?)");
            $stmt_parcela->bind_param("isddd", $id_compra, $data_vencimento_formatada, $referencia_parcela, $valor_resp1, $valor_resp2);
            $stmt_parcela->execute();
            $stmt_parcela->close();

            $data_vencimento->modify('+1 month');
        }

        echo json_encode(['sucesso' => true, 'mensagem' => 'Compra e parcelamentos cadastrados com sucesso!']);
    } else {
        echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao cadastrar a compra: ' . $stmt_compra->error]);
    }

    $stmt_compra->close();
} else {
    echo json_encode(['sucesso' => false, 'mensagem' => 'Acesso inválido.']);
}

$conn->close();
