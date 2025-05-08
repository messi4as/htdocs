<?php
session_start();
require 'db_connect.php';

// Função para escapar e validar dados
function validarDados($dado)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($dado));
}

// Função auxiliar para desformatar o valor (remova pontos e substitua vírgula por ponto)
function unformat_valor($valor_formatado)
{
    return str_replace(',', '.', str_replace('.', '', $valor_formatado));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obter e validar os dados do formulário
    $id_compra = filter_input(INPUT_POST, 'id_compra', FILTER_SANITIZE_NUMBER_INT);
    $nome_cartao = validarDados($_POST['nome_cartao']);
    $data_compra = validarDados($_POST['data_compra']);
    $quantidade_parcelas = filter_input(INPUT_POST, 'quantidade_parcelas', FILTER_SANITIZE_NUMBER_INT);
    $valor = unformat_valor(validarDados($_POST['valor']));
    $primeira_vencimento = validarDados($_POST['primeira_vencimento']);
    $percentual_responsavel1 = filter_input(INPUT_POST, 'percentual_responsavel1', FILTER_SANITIZE_NUMBER_INT) / 100;
    $percentual_responsavel2 = filter_input(INPUT_POST, 'percentual_responsavel2', FILTER_SANITIZE_NUMBER_INT) / 100;
    $descricao = validarDados($_POST['descricao']);

    
    // Validações adicionais
    if ($quantidade_parcelas <= 0) {
        $resposta = ['sucesso' => false, 'mensagem' => "A quantidade de parcelas deve ser maior que zero."];
        echo json_encode($resposta);
        exit();
    }

    if ($percentual_responsavel1 + $percentual_responsavel2 != 1) {
        $resposta = ['sucesso' => false, 'mensagem' => "A soma dos percentuais dos responsáveis deve ser igual a 100%."];
        echo json_encode($resposta);
        exit();
    }

    

    // Iniciar transação
    mysqli_begin_transaction($conn);
    $erro = false;

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

    // Atualizar a tabela de compras
    $sql_atualizar_compra = "UPDATE compras SET
                                        nome_cartao = '$nome_cartao',
                                        data_compra = '$data_compra',
                                        valor = $valor,
                                        quantidade_parcelas = $quantidade_parcelas,
                                        descricao = '$descricao'
                                        WHERE id_compra = $id_compra";

    if (!mysqli_query($conn, $sql_atualizar_compra)) {
        $erro = true;
        $_SESSION['mensagem'] = "Erro ao atualizar os dados da compra: " . mysqli_error($conn);
        $_SESSION['tipo_mensagem'] = "danger";
    }

    // Lidar com os anexos (mesma lógica do seu código)
    $anexos_existentes_sql = "SELECT anexo FROM compras WHERE id_compra = $id_compra";
    $resultado_anexos_existentes = mysqli_query($conn, $anexos_existentes_sql);
    $anexos_existentes_array = json_decode(mysqli_fetch_assoc($resultado_anexos_existentes)['anexo'], true) ?? [];
    $novos_anexos = [];
    $upload_dir = 'uploads/documentos/';

    if (isset($_FILES['anexo']) && is_array($_FILES['anexo']['name'])) {
        $num_files = count($_FILES['anexo']['name']);
        for ($i = 0; $i < $num_files; $i++) {
            if ($_FILES['anexo']['error'][$i] == 0) {
                $nome_arquivo = $_FILES['anexo']['name'][$i];
                $caminho_temporario = $_FILES['anexo']['tmp_name'][$i];
                $novo_caminho = $upload_dir . uniqid() . '_' . $nome_arquivo;
                if (move_uploaded_file($caminho_temporario, $novo_caminho)) {
                    $novos_anexos[] = $novo_caminho;
                } else {
                    $erro = true;
                    $_SESSION['mensagem'] = "Erro ao fazer upload de um dos anexos.";
                    $_SESSION['tipo_mensagem'] = "danger";
                    break;
                }
            } elseif ($_FILES['anexo']['error'][$i] != 4) {
                $erro = true;
                $_SESSION['mensagem'] = "Erro no upload de um dos anexos.";
                $_SESSION['tipo_mensagem'] = "danger";
                break;
            }
        }
    }

    $anexos_finais = array_merge($anexos_existentes_array, $novos_anexos);
    $anexos_json = json_encode($anexos_finais);
    $sql_atualizar_anexos = "UPDATE compras SET anexo = '$anexos_json' WHERE id_compra = $id_compra";
    if (!mysqli_query($conn, $sql_atualizar_anexos)) {
        $erro = true;
        $_SESSION['mensagem'] = "Erro ao atualizar os anexos: " . mysqli_error($conn);
        $_SESSION['tipo_mensagem'] = "danger";
    }

    // Excluir os parcelamentos existentes
    $sql_excluir_parcelamentos = "DELETE FROM parcelamentos WHERE id_compra = $id_compra";
    if (!mysqli_query($conn, $sql_excluir_parcelamentos)) {
        $erro = true;
        $_SESSION['mensagem'] = "Erro ao excluir os parcelamentos antigos: " . mysqli_error($conn);
        $_SESSION['tipo_mensagem'] = "danger";
    }

    // Recalcular e inserir os novos parcelamentos com ajuste na primeira parcela
    $valor_parcela_exato = $valor / $quantidade_parcelas;
    $valor_parcela_padrao = round($valor_parcela_exato, 2);
    $soma_parcelas_padrao = ($quantidade_parcelas - 1) * $valor_parcela_padrao;
    $valor_primeira_parcela = round($valor - $soma_parcelas_padrao, 2);
    $data_vencimento = $primeira_vencimento;
    $valor_total_numerico = floatval($valor); // Garante que o valor total seja numérico

    for ($i = 1; $i <= $quantidade_parcelas; $i++) {
        $valor_parcela_atual = ($i === 1) ? $valor_primeira_parcela : $valor_parcela_padrao;
        $valor_resp1 = round($valor_parcela_atual * $percentual_responsavel1, 2);
        $valor_resp2 = round($valor_parcela_atual * $percentual_responsavel2, 2);

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

        $sql_inserir_parcela = "INSERT INTO parcelamentos (id_compra, referencia_parcela, data_vencimento, valor_parcela_responsavel1, valor_parcela_responsavel2)
                                            VALUES ($id_compra, $i, '$data_vencimento', $valor_resp1, $valor_resp2)";
        if (!mysqli_query($conn, $sql_inserir_parcela)) {
            $erro = true;
            $_SESSION['mensagem'] = "Erro ao inserir o parcelamento $i: " . mysqli_error($conn);
            $_SESSION['tipo_mensagem'] = "danger";
            break;
        }
        $data_vencimento = date('Y-m-d', strtotime('+1 month', strtotime($data_vencimento)));
    }

    if ($erro) {
        mysqli_rollback($conn);
        $resposta = ['sucesso' => false, 'mensagem' => $_SESSION['mensagem']];
    } else {
        mysqli_commit($conn);
        $_SESSION['mensagem'] = "Compra e parcelamentos atualizados com sucesso!";
        $_SESSION['tipo_mensagem'] = "success";
        $resposta = ['sucesso' => true, 'mensagem' => $_SESSION['mensagem']];
    }

    mysqli_close($conn);
    echo json_encode($resposta);
} else {
    $resposta = ['sucesso' => false, 'mensagem' => "Método de requisição inválido."];
    echo json_encode($resposta);
}
