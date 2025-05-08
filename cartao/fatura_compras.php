<?php
session_start();
require 'db_connect.php';

// Função para escapar e validar dados
function validarDados($dado)
{
    global $conn;
    return mysqli_real_escape_string($conn, trim($dado));
}

// Função para formatar o valor para exibição
function formatar_valor($valor)
{
    return number_format($valor, 2, ',', '.');
}

// Parâmetros de filtro
$nome_cartao_filtro = '';
if (isset($_GET['nome_cartao'])) {
    $nome_cartao_filtro = validarDados($_GET['nome_cartao']);
}
$data_vencimento_filtro = '';
if (isset($_GET['data_vencimento'])) {
    $data_vencimento_filtro = validarDados($_GET['data_vencimento']);
}

// Consulta SQL para obter as parcelas filtradas
$sql = "SELECT
            c.data_compra,
            c.descricao,
            c.valor AS valor_compra,
            p.referencia_parcela,
            p.data_vencimento,
            p.valor_parcela_responsavel1,
            p.valor_parcela_responsavel2,
            c.quantidade_parcelas
        FROM
            parcelamentos p
        JOIN
            compras c ON p.id_compra = c.id_compra
        WHERE 1=1"; // Garante que a condição WHERE sempre comece com algo verdadeiro

if (!empty($nome_cartao_filtro)) {
    $sql .= " AND c.nome_cartao = '$nome_cartao_filtro'";
}
if (!empty($data_vencimento_filtro)) {
    $sql .= " AND p.data_vencimento = '$data_vencimento_filtro'";
}

$sql .= " ORDER BY p.data_vencimento"; // Ordena por data de vencimento.

$resultado = $conn->query($sql);
$parcelas = [];
if ($resultado->num_rows > 0) {
    while ($parcela = $resultado->fetch_assoc()) {
        $parcela['valor_parcela_responsavel1_formatado'] = formatar_valor($parcela['valor_parcela_responsavel1']);
        $parcela['valor_parcela_responsavel2_formatado'] = formatar_valor($parcela['valor_parcela_responsavel2']);
        $parcela['valor_compra_formatado'] = formatar_valor($parcela['valor_compra']);
        $parcelas[] = $parcela;
    }
}

// Calcular os totais por responsável
$total_responsavel1 = 0;
$total_responsavel2 = 0;
foreach ($parcelas as $parcela) {
    $total_responsavel1 += $parcela['valor_parcela_responsavel1'];
    $total_responsavel2 += $parcela['valor_parcela_responsavel2'];
}
$total_responsavel1_formatado = formatar_valor($total_responsavel1);
$total_responsavel2_formatado = formatar_valor($total_responsavel2);

// Calcular o total geral
$total_geral = $total_responsavel1 + $total_responsavel2;
$total_geral_formatado = formatar_valor($total_geral);

?>

<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <title>Fatura de Compras</title>
    <style>
        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        /* Estilos para impressão */
        @media print {
            body * {
                visibility: visible;
            }

            .no-print {
                display: none;
            }

            .print-section {
                position: relative;
                width: 100%;
                top: 0;
                left: 0;
            }

            table {
                width: 100% !important;
                border-collapse: collapse;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }

            body {
                font-size: 10pt;
            }

            .filtro-container {
                display: flex;
                justify-content: space-between;
                margin-bottom: 10px;
                width: 100%;
            }

            .filtro-item {
                width: 48%;
                display: inline-block;
                margin-right: 10px;
            }

            .filtro-item p {
                margin-bottom: 0;
            }
        }

        .filtro-container {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            width: 100%;
        }

        .filtro-item {
            margin-right: 10px;
            margin-bottom: 0;
        }

        .filtro-item label {
            margin-right: 5px;
        }
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4 print-section">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header no-print">
                            <h4>FATURA DE CARTÕES
                                <a href="lista_compras.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form method="GET" action="fatura_compras.php" class="no-print">
                                <div class="form-row filtro-container">
                                    <div class="form-group filtro-item">
                                        <label for="nome_cartao">Nome do Cartão:</label>
                                        <select name="nome_cartao" id="nome_cartao" class="form-control">
                                            <option value="">Todos os Cartões</option>
                                            <option value="CARTÃO M2" <?= ($nome_cartao_filtro == 'CARTÃO M2') ? 'selected' : '' ?>>CARTÃO M2</option>
                                            <option value="CARTÃO MAIARA CARLA" <?= ($nome_cartao_filtro == 'CARTÃO MAIARA CARLA') ? 'selected' : '' ?>>CARTÃO MAIARA CARLA</option>
                                            <option value="CARTÃO CARLA MARAISA" <?= ($nome_cartao_filtro == 'CARTÃO CARLA MARAISA') ? 'selected' : '' ?>>CARTÃO CARLA MARAISA</option>
                                            <option value="CARTÃO BNDES" <?= ($nome_cartao_filtro == 'CARTÃO BNDES') ? 'selected' : '' ?>>CARTÃO BNDES</option>
                                        </select>
                                    </div>
                                    <div class="form-group filtro-item">
                                        <label for="data_vencimento">Data de Vencimento:</label>
                                        <input type="date" name="data_vencimento" id="data_vencimento" class="form-control" value="<?= $data_vencimento_filtro ?>">
                                    </div>
                                    <div class="form-group filtro-item">
                                        <button type="submit" class="btn btn-primary mt-3"><span class="bi-filter"></span>&nbsp;Filtrar</button>
                                    </div>
                                    <div class="form-group filtro-item">
                                        <button id="print-button" class="btn btn-success mt-3"><span class="bi-printer"></span>&nbsp;Imprimir</button>
                                    </div>
                                </div>
                            </form>

                            <div id="filtro-container-print" class="filtro-container" style="display: none;">
                                <div class="filtro-item">
                                    <?php
                                    echo "<p><strong>Filtro Nome Cartão:</strong> " . (!empty($nome_cartao_filtro) ? $nome_cartao_filtro : 'Todos') . "</p>";
                                    ?>
                                </div>
                                <div class="filtro-item">
                                    <?php
                                    echo "<p><strong>Filtro Data Vencimento:</strong> " . (!empty($data_vencimento_filtro) ? date('d/m/Y', strtotime($data_vencimento_filtro)) : 'Todas') . "</p>";
                                    ?>
                                </div>
                            </div>

                            <h5 class="mt-4">Parcelas Encontradas:</h5>
                            <?php if (!empty($parcelas)) : ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>DATA DA COMPRA</th>
                                            <th>DESCRIÇÃO</th>
                                            <th>VALOR DA COMPRA</th>
                                            <th>PARCELA</th>
                                            <th>VENCIMENTO</th>
                                            <th>MAIARA CARLA</th>
                                            <th>CARLA MARAISA</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($parcelas as $parcela) : ?>
                                            <tr>
                                                <td class="text-center"><?= date('d/m/Y', strtotime($parcela['data_compra'])); ?></td>
                                                <td class="text-left"><?= htmlspecialchars($parcela['descricao']); ?></td>
                                                <td class="text-center"><?= $parcela['valor_compra_formatado'] ?></td>
                                                <td class="text-center"><?= htmlspecialchars($parcela['referencia_parcela']) . " de " . htmlspecialchars($parcela['quantidade_parcelas']); ?></td>
                                                <td class="text-center"><?= date('d/m/Y', strtotime($parcela['data_vencimento'])); ?></td>
                                                <td class="text-center"><?= $parcela['valor_parcela_responsavel1_formatado']; ?></td>
                                                <td class="text-center"><?= $parcela['valor_parcela_responsavel2_formatado']; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <tr>
                                            <td colspan="5" class="text-right"><strong>TOTAL:</strong></td>
                                            <td class="text-center"><strong><?= $total_responsavel1_formatado; ?></strong></td>
                                            <td class="text-center"><strong><?= $total_responsavel2_formatado; ?></strong></td>
                                        </tr>
                                    </tbody>

                                    <tfoot>
                                        <tr>
                                            <td colspan="6" class="text-right"><strong>TOTAL GERAL:</strong></td>
                                            <td class="text-center"><strong><?= $total_geral_formatado; ?></strong></td>
                                        </tr>
                                    </tfoot>



                                </table>
                            <?php else : ?>
                                <p class="text-center">Nenhuma parcela encontrada para os filtros selecionados.</p>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    window.onload = function() {
        var printButton = document.getElementById('print-button'); // Seletor para o botão de imprimir
        var filtroContainerPrint = document.getElementById('filtro-container-print');

        if (printButton && filtroContainerPrint) {
            printButton.addEventListener('click', function() {
                filtroContainerPrint.style.display = 'flex'; // Exibe os filtros antes de imprimir
                setTimeout(function() {
                    window.print(); // Chama a função de impressão
                    filtroContainerPrint.style.display = 'none'; // Oculta os filtros após a impressão
                }, 0); // Usa setTimeout para garantir que o estilo seja aplicado antes da impressão
            });
        } else {
            console.error("Botão de impressão ou container de filtro para impressão não encontrado.");
        }
    };
</script>

</html>