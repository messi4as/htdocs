<?php
session_start();
require 'db_connect.php';

// --- LÓGICA DE BUSCA ORIGINAL ---
$nome = '';
if (isset($_GET['nome_item'])) {
    $nome = mysqli_real_escape_string($conn, $_GET['nome_item']);
}

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
$sql .= " ORDER BY nome_item ASC";

$result = $conn->query($sql);
$item_query = mysqli_query($conn, $sql);
$quantidade_total = mysqli_num_rows($item_query);

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
    <title>LISTA DE ITENS</title>
   <style>
        .img-clickable { cursor: pointer; }
        .carousel-item img { max-height: 500px; object-fit: contain; width: 100%; background-color: #000; }
        table th, table td { text-align: center; vertical-align: middle !important; }
        .badge-quantidade { background-color: #e3f2fd; color: #0d6efd; padding: 5px 10px; border-radius: 5px; font-weight: bold; }

        /* DESTAQUE PARA QUANTIDADE ZERO */
        .linha-esgotada td {
            background-color: #f8d7da !important;
            color: #721c24 !important;
        }

        /* REGRAS DE IMPRESSÃO */
        @media print {
            .no-print {
                display: none !important;
            }
            .card { border: none !important; }
            body { background-color: white !important; }
            .container-fluid { padding: 0 !important; }
            /* Garante que o destaque vermelho saia na impressão em alguns navegadores */
            .linha-esgotada td {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        :root {
      --btn-color: rgb(175, 166, 118); /* Cor dourada em RGB */
    }
           .btn-custom {
      background-color: var(--btn-color);
      color: black;
      border: none;
      padding: 7px 20px;
      font-size: 16px;
      cursor: pointer;
    }
     .btn-custom:hover {
      background-color: rgb(211, 191, 81); /* Cor darkgoldenrod em RGB */
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
                    <div class="card-header">
                        <h4>LISTA DE ITENS ARMAZENADOS NO GALPÃO
                           
                            <a href="cadastro_item.php" class="btn btn-primary float-end no-print"><span class="bi-plus-circle-fill"></span>&nbsp;Adicionar Item</a>
                            <button onclick="window.print()" class="btn btn-info float-end me-2 no-print"><span class="bi-printer-fill"></span>&nbsp;Imprimir</button>
                             <a href="planta.php" class="btn btn-custom float-end me-2 no-print"><span class="bi bi-card-checklist"></span>&nbsp;Planta</a>
                        </h4>

                    </div>
                    <div class="card-body">
                        <form action="" method="GET" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="text" name="nome_item" value="<?= $nome ?>" class="form-control" placeholder="Buscar por Nome">
                                </div>


                                <div class="col-md-4">
                                    <select name="local" class="form-control">
                                        <option value="">Selecione o Local</option>
                                        <?php
                                        $locais = getOptions($conn, 'local');
                                        foreach ($locais as $l) {
                                            echo "<option value='$l' " . ($local == $l ? 'selected' : '') . ">$l</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-4 no-print">
                                    <button type="submit" class="btn btn-info text-white no-print">Pesquisar</button>
                                    <a href="lista_item.php" class="btn btn-danger no-print">Limpar</a>
                                </div>


                            </div>


                        </form>

                        <div class="alert alert-info" role="alert">
                            Quantidade de Itens Cadastrados: <?php echo number_format($quantidade_total, 0, ',', '.'); ?>
                        </div>

                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>FOTO</th>
                                    <th>NOME</th>
                                    <th>CATEGORIA</th>
                                    <th>LOCAL</th>
                                    <th>QUANTIDADE</th>
                                    <th class="no-print">AÇÕES</th>
                                </tr>
                           </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): 
                                    $classe_esgotado = ($row['quantidade'] <= 0) ? 'linha-esgotada' : '';
                                ?>
                                <tr class="<?= $classe_esgotado ?>">
                                    <td>
                                        <?php if ($row['foto']): ?>
                                            <img src="<?= $row['foto']; ?>" 
                                                 class="img-thumbnail img-clickable" 
                                                 style="width: 80px;"
                                                 data-bs-toggle="modal" 
                                                 data-bs-target="#imageModal"
                                                 data-item-id="<?= $row['cod_item']; ?>"
                                                 data-nome="<?= $row['nome_item']; ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td><?= $row['nome_item']; ?></td>
                                    <td><?= $row['categoria']; ?></td>
                                    <td><?= $row['local']; ?></td>
                                    <td><span class="badge-quantidade"><?= $row['quantidade']; ?></span></td>
                                    <td class="no-print">
                                        <a href="edit_item.php?id=<?= $row['cod_item']; ?>" class="btn btn-success btn-sm">
                                            <i class="bi bi-pencil-square"></i> Editar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade no-print" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Galeria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-dark p-0">
                    <div id="carouselGaleria" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner" id="carouselContent"></div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleria" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleria" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var imageModal = document.getElementById('imageModal');
            imageModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget;
                var itemId = button.getAttribute('data-item-id');
                var nomeItem = button.getAttribute('data-nome');
                var carouselInner = document.getElementById('carouselContent');
                document.getElementById('modalTitle').textContent = nomeItem;

                carouselInner.innerHTML = '<div class="text-center p-5 text-white"><div class="spinner-border"></div></div>';

                fetch('buscar_galeria.php?id=' + itemId)
                    .then(response => response.json())
                    .then(imagens => {
                        carouselInner.innerHTML = '';
                        imagens.forEach((src, index) => {
                            var div = document.createElement('div');
                            div.className = 'carousel-item' + (index === 0 ? ' active' : '');
                            div.innerHTML = `<img src="${src}" class="d-block">`;
                            carouselInner.appendChild(div);
                        });
                    });
            });
        });
    </script>
</body>

</html>