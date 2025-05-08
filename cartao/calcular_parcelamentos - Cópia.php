<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome_cartao = htmlspecialchars($_POST['nome_cartao']);
    $data_compra = htmlspecialchars($_POST['data_compra']);
    $quantidade_parcelas = intval(htmlspecialchars($_POST['quantidade_parcelas']));
    $valor_total_texto = htmlspecialchars($_POST['valor']);
    $valor_total = str_replace(['R$ ', '.', ','], ['', '', '.'], $valor_total_texto);
    $descricao = strtoupper(htmlspecialchars($_POST['descricao']));
    $primeira_vencimento = htmlspecialchars($_POST['primeira_vencimento']);
    $percentual_responsavel1 = floatval(htmlspecialchars($_POST['percentual_responsavel1'])) / 100;
    $percentual_responsavel2 = floatval(htmlspecialchars($_POST['percentual_responsavel2'])) / 100;

    $anexos = [];
    if (isset($_FILES['anexo'])) {
        foreach ($_FILES['anexo']['name'] as $name) {
            if (!empty($name)) {
                $anexos[] = $name; // Apenas os nomes para exibição no modal
            }
        }
    }

    if ($quantidade_parcelas <= 0 || !is_numeric($valor_total) || $valor_total <= 0) {
        echo json_encode(['erro' => true, 'mensagem' => 'Quantidade de parcelas ou valor inválido.']);
        exit();
    }

    $valor_parcela_exato = $valor_total / $quantidade_parcelas;
    $valor_parcela_padrao = round($valor_parcela_exato, 2);
    $data_vencimento = new DateTime($primeira_vencimento);
    $parcelamentos = [];
    $soma_parcelas_padrao = ($quantidade_parcelas - 1) * $valor_parcela_padrao;
    $valor_primeira_parcela = round($valor_total - $soma_parcelas_padrao, 2);

    for ($i = 1; $i <= $quantidade_parcelas; $i++) {
        $referencia_parcela = "Parcela " . $i . " de " . $quantidade_parcelas;
        $valor_parcela_atual = ($i === 1) ? $valor_primeira_parcela : $valor_parcela_padrao;

        $valor_resp1 = 'R$ ' . number_format(round($valor_parcela_atual * $percentual_responsavel1, 2), 2, ',', '.');
        $valor_resp2 = 'R$ ' . number_format(round($valor_parcela_atual * $percentual_responsavel2, 2), 2, ',', '.');
        $data_vencimento_formatada = $data_vencimento->format('d/m/Y');

        $parcelamentos[] = [
            'referencia_parcela' => $referencia_parcela,
            'data_vencimento' => $data_vencimento_formatada,
            'valor_parcela_responsavel1' => $valor_resp1,
            'valor_parcela_responsavel2' => $valor_resp2,
        ];

        $data_vencimento->modify('+1 month');
    }

    $response = [
        'erro' => false,
        'compra' => [
            'nome_cartao' => $nome_cartao,
            'data_compra' => date('d/m/Y', strtotime($data_compra)),
            'valor' => $valor_total_texto,
            'quantidade_parcelas' => $quantidade_parcelas,
            'primeira_vencimento' => date('d/m/Y', strtotime($primeira_vencimento)),
            'descricao' => $descricao,
            'anexos' => $anexos
        ],
        'parcelamentos' => $parcelamentos
    ];

    echo json_encode($response);
    exit();
} else {
    echo json_encode(['erro' => true, 'mensagem' => 'Acesso inválido.']);
    exit();
}
?>