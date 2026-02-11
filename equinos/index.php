<?php
session_start();
require 'db_connect.php';

// Filtros para os Selects
$status_query = "SELECT DISTINCT status FROM equinos ORDER BY status ASC";
$status_result = mysqli_query($conn, $status_query);

$local_query = "SELECT DISTINCT local FROM equinos ORDER BY local ASC";
$local_result = mysqli_query($conn, $local_query);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>EQUINOS - FAZENDA ROSADA</title>
    <style>
        .img-thumb { 
            width: 60px; height: 60px; object-fit: cover; 
            border-radius: 8px; cursor: pointer; border: 1px solid #ddd;
        }
        .img-thumb:hover { opacity: 0.8; border-color: #0d6efd; }
        .carousel-item img { height: 500px; object-fit: contain; background: #000; }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>GESTÃO DE EQUINOS
                    <a href="cad_equino.php" class="btn btn-primary float-end"><span class="bi-plus-circle"></span> Novo Equino</a>
                </h4>
            </div>
            <div class="card-body">
                <form method="GET" class="mb-3">
                    <div class="input-group">
                        <input type="text" name="busca" class="form-control" placeholder="Nome, Registro ou Proprietário" value="<?= $_GET['busca'] ?? '' ?>">
                        <select name="status" class="form-control">
                            <option value="">Status (Todos)</option>
                            <?php while ($row = mysqli_fetch_assoc($status_result)) echo "<option value='{$row['status']}'>{$row['status']}</option>"; ?>
                        </select>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>FOTO</th>
                <th>NOME</th>
                <th>REGISTRO</th>
                <th>RAÇA / PELAGEM</th>
                <th>IDADE (MESES)</th> <th>STATUS</th>
                <th>AÇÕES</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $busca = mysqli_real_escape_string($conn, $_GET['busca'] ?? '');
            $sql = "SELECT * FROM equinos WHERE nome_animal LIKE '%$busca%' OR num_registro LIKE '%$busca%' ORDER BY nome_animal ASC";
            $equinos = mysqli_query($conn, $sql);

            while ($item = mysqli_fetch_array($equinos)):
                $id_eq = $item['cod_equino'];

                // LÓGICA PARA CALCULAR A IDADE EM MESES
                $idade_exibicao = "-";
                if (!empty($item['data_nascimento']) && $item['data_nascimento'] != '0000-00-00') {
                    $nascimento = new DateTime($item['data_nascimento']);
                    $hoje = new DateTime();
                    $diferenca = $nascimento->diff($hoje);
                    
                    // Cálculo total de meses: (anos * 12) + meses
                    $total_meses = ($diferenca->y * 12) + $diferenca->m;
                    $idade_exibicao = $total_meses . " m";
                }
            ?>
                <tr>
                    <td>
                        <img src="uploads/imagens/<?= $item['foto_capa'] ?: 'default.png' ?>" 
                             class="img-thumb" 
                             data-bs-toggle="modal" 
                             data-bs-target="#modalGaleria<?= $id_eq ?>">
                    </td>
                    <td><strong><?= $item['nome_animal'] ?></strong></td>
                    <td><?= $item['num_registro'] ?></td>
                    <td><?= $item['raca'] ?> / <?= $item['pelagem'] ?></td>
                    <td><?= $idade_exibicao ?></td> <td>
                        <span class="badge <?= $item['status'] == 'ATIVO' ? 'bg-success' : 'bg-danger' ?>">
                            <?= $item['status'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="view_equino.php?id=<?= $id_eq ?>" class="btn btn-secondary btn-sm">
                            <i class="bi bi-eye"></i> Visualizar
                        </a>
                    </td>
                </tr>

                                <div class="modal fade" id="modalGaleria<?= $id_eq ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content bg-dark">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title text-white"><?= $item['nome_animal'] ?> - Galeria</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-0">
                                                <div id="carousel<?= $id_eq ?>" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-inner">
                                                        <?php
                                                        $fotos_res = mysqli_query($conn, "SELECT * FROM fotos_equinos WHERE cod_equino = '$id_eq'");
                                                        $primeira = true;
                                                        if(mysqli_num_rows($fotos_res) > 0){
                                                            while($f = mysqli_fetch_array($fotos_res)):
                                                        ?>
                                                            <div class="carousel-item <?= $primeira ? 'active' : '' ?>">
                                                                <img src="uploads/imagens/<?= $f['caminho'] ?>" class="d-block w-100">
                                                            </div>
                                                        <?php $primeira = false; endwhile; 
                                                        } else { ?>
                                                            <div class="carousel-item active">
                                                                <img src="uploads/imagens/default.png" class="d-block w-100">
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <button class="carousel-control-prev" type="button" data-bs-target="#carousel<?= $id_eq ?>" data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button" data-bs-target="#carousel<?= $id_eq ?>" data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>