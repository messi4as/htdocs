<?php
session_start();
require 'db_connect.php';


// Obter todos os itens do banco de dados
$nome = '';
if (isset($_GET['nome_item'])) {
    $nome = mysqli_real_escape_string($conn, $_GET['nome_item']);
}

// Obter o local a partir da URL
$local = '';
if (isset($_GET['local'])) {
    $local = mysqli_real_escape_string($conn, $_GET['local']);
}

$sql = "SELECT * FROM galpao";
if ($nome != '') {
    $sql .= " WHERE nome_item LIKE '%$nome%'";
}
if ($local != '') {
    $sql .= $nome != '' ? " AND local LIKE '%$local%'" : " WHERE local LIKE '%$local%'";
}
$sql .= " ORDER BY local, nome_item ASC";
$result = $conn->query($sql);
$item = mysqli_query($conn, $sql);

$quantidade = mysqli_num_rows($item);


// Função para obter opções únicas de uma coluna
function getOptions($conn, $column)
{
    $options = [];
    $sql = "SELECT DISTINCT $column FROM galpao";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row[$column];
    }
    return $options;
}

$localOptions = getOptions($conn, 'local');
// Função para obter dados dos itens com filtros
function getBovinosData($conn, $local)
{
    $sql = "SELECT local COUNT(*) as quantidade 
            FROM galpao 
            WHERE 1=1";
    if ($local != '') {
        $conditions[] = "local LIKE '%$local%'";
    }

    $sql .= " GROUP BY local";
    $result = mysqli_query($conn, $sql);
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    return $data;
}


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

    <title>Galpão</title>

    <style>
        /* Estilos para impressão */
        @media print {
            body {
                padding: 0;
                margin: 0;
                font-size: 11pt;
            }

            .no-print,
            .d-print-none {
                display: none !important;
            }

            /* Limpa estilos de Bootstrap para que não interfiram */
            .container,
            .row,
            .col-md-12,
            .card {
                width: 100% !important;
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                box-shadow: none !important;
            }

            /* Centraliza o cabeçalho H4 */
            h4 {
                text-align: center;
                margin-bottom: 20px;
                margin: 0 auto;
                /* Centraliza horizontalmente */
            }

            /* Define o contêiner da tabela para centralizá-la */
            .table-container {
                width: 95%;
                /* Define a largura para centralizar */
                margin: 0 auto;
                /* Centraliza a tabela na página */
                overflow-x: visible !important;
            }

            table {
                width: 100%;
                /* Garante que a tabela ocupe 100% do seu contêiner pai */
                border-collapse: collapse;
                page-break-inside: auto;
                table-layout: fixed;
            }

            th,
            td {
                border: 1px solid #000;
                padding: 5px;
                font-size: 10pt;
                word-wrap: break-word;
            }

            th {
                background-color: #f2f2f2;
                font-weight: bold;
                text-align: center;
            }

            td {
                text-align: left;
                vertical-align: top;
            }

            td img {
                width: 100px;
                height: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>



</head>

<body>

    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header no-print">

                            <h4>LISTA DE ITENS ARMAZENADOS NO GALPÃO
                                <a href="cadastro_item.php" class="btn btn-primary float-end"><span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Item</a>
                                <button onclick="window.print()" class="btn btn-info float-end me-2"><span class="bi-printer-fill"></span>&nbsp;Imprimir</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="no-print">
                                <form method="GET" action="lista_item.php">
                                    <div class="input-group mb-3">
                                        <input type="text" name="nome_item" class="form-control" placeholder="Buscar por Nome">
                                        <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                                    </div>
                                </form>
                                <form method="GET" action="lista_item.php" class="d-flex align-items-center">
                                    <select name="local" class="form-control me-2">
                                        <option value="">Local</option>
                                        <?php foreach ($localOptions as $option) : ?>
                                            <option value="<?= $option ?>" <?= $local == $option ? 'selected' : '' ?>><?= $option ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button class="btn btn-primary" type="submit">Filtrar</button>
                                </form>
                                <br>
                                <div class="alert alert-info" role="alert">
                                    Quantidade de Itens Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                                </div>
                            </div>

                            <div class="d-none d-print-block">
                                <p><strong>Filtros Aplicados:</strong></p>
                                <ul>
                                    <li><strong>Filtro por Local:</strong> <?= !empty($local) ? htmlspecialchars($local) : 'Nenhum' ?></li>
                                    <li><strong>Total de Itens:</strong> <?php echo number_format($quantidade, 0, ',', '.'); ?></li>
                                </ul>
                                <hr>
                            </div>

                            <h4 class="d-none d-print-block">RELATÓRIO DE ITENS DO GALPÃO</h4>

                            <table class="table table-bordered table-striped table-hover table-sm table-responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align: middle;">FOTO</th>
                                        <th style="text-align: center; vertical-align: middle;">NOME</th>
                                        <th style="text-align: center; vertical-align: middle;">CATEGORIA</th>
                                        <th style="text-align: center; vertical-align: middle;">ORIGEM</th>
                                        <th style="text-align: center; vertical-align: middle;">QUANTIDADE</th>
                                        <th style="text-align: center; vertical-align: middle;">DATA DE ENTRADA</th>
                                        <th style="text-align: center; vertical-align: middle;">LOCAL</th>
                                        <th class="no-print" style="text-align: center; vertical-align: middle;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td>
                                                    <?php if ($row['foto']): ?>
                                                        <img src="<?= $row['foto']; ?>" 
                                                             alt="Foto do Item" 
                                                             style="width: 100px; height: auto; cursor: pointer;"
                                                             class="img-clickable"
                                                             data-bs-toggle="modal"
                                                             data-bs-target="#imageModal"
                                                             data-full-img="<?= $row['foto']; ?>">
                                                    <?php endif; ?>
                                                </td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['nome_item']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['categoria']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['origem']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= $row['quantidade']; ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($row['data_entrada'])); ?></td>
                                                <td style="text-align: center; vertical-align: middle;"><?= ($row['local']); ?></td>
                                                <td class="no-print" style="text-align: center; vertical-align: middle;">
                                                    <a href="edit_item.php?id=<?= $row['cod_item'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="8" style="text-align: center;">Nenhum Item encontrado</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog **modal-md** modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">Visualização da Imagem</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="" id="fullImage" class="img-fluid" alt="Imagem Ampliada">
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Pega o elemento do Modal e o elemento <img> dentro dele
                var imageModal = document.getElementById('imageModal');
                var fullImage = document.getElementById('fullImage');

                // Adiciona um listener para o evento 'show.bs.modal' do Bootstrap (disparado antes de o modal ser exibido)
                imageModal.addEventListener('show.bs.modal', function (event) {
                    // Botão que disparou o modal (a tag <img> clicada)
                    var relatedTarget = event.relatedTarget;
                    
                    // Pega o valor do atributo 'data-full-img' da tag <img> clicada
                    var imageUrl = relatedTarget.getAttribute('data-full-img');

                    // Define o src da imagem dentro do modal
                    fullImage.src = imageUrl;
                    
                    // Opcional: define o nome do arquivo como título, pegando da célula da tabela
                    // (td:nth-child(2) é a segunda coluna, que é o NOME)
                    var itemNomeCell = relatedTarget.closest('tr').querySelector('td:nth-child(2)');
                    var itemNome = itemNomeCell ? itemNomeCell.textContent.trim() : '';
                    var modalTitle = imageModal.querySelector('.modal-title');
                    
                    if (itemNome) {
                         modalTitle.textContent = 'Imagem do Item: ' + itemNome;
                    } else {
                         modalTitle.textContent = 'Visualização da Imagem';
                    }
                });

                // Limpa o src da imagem quando o modal for escondido para liberar memória/evitar erros
                imageModal.addEventListener('hidden.bs.modal', function (event) {
                    fullImage.src = '';
                });
            });
        </script>
</body>

</html>