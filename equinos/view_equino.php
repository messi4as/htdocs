<?php
session_start();
require 'db_connect.php';

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM equinos WHERE cod_equino = '$id'";
$resultado = mysqli_query($conn, $sql);
$equino = mysqli_fetch_array($resultado);

if (!$equino) {
    header("Location: index.php");
    exit(0);
}

// Lógica para calcular a idade detalhada
$idade_txt = "Não informada";
if (!empty($equino['data_nascimento']) && $equino['data_nascimento'] != '0000-00-00') {
    $nasc = new DateTime($equino['data_nascimento']);
    $hoje = new DateTime();
    $diff = $nasc->diff($hoje);
    $idade_txt = $diff->y . " anos e " . $diff->m . " meses";
}
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Visualizar Equino - <?= $equino['nome_animal'] ?></title>
    <style>
        .foto-galeria { height: 150px; object-fit: cover; width: 100%; border-radius: 5px; }
        .capa-container { height: 250px; display: flex; align-items: center; justify-content: center; background: #f8f9fa; border: 2px dashed #dee2e6; border-radius: 10px; overflow: hidden; }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <?php include('mensagem.php'); ?>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h4>
                    <i class="bi bi-info-circle"></i> VISUALIZAR: <?= mb_strtoupper($equino['nome_animal']) ?>
                    <span class="badge bg-info text-dark ms-2" style="font-size: 0.5em; vertical-align: middle;">
                        Idade: <?= $idade_txt ?>
                    </span>
                    <a href="index.php" class="btn btn-danger float-end btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="code_equino.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="cod_equino" value="<?= $equino['cod_equino'] ?>">
                    
                    <div class="row">
                        <div class="col-md-3 text-center border-end">
                            <div class="capa-container mb-2">
                                <?php if($equino['foto_capa']): ?>
                                    <img src="uploads/imagens/<?= $equino['foto_capa'] ?>" class="img-fluid">
                                <?php else: ?>
                                    <span class="text-muted">Sem Foto de Capa</span>
                                <?php endif; ?>
                            </div>
                            <label class="form-label small fw-bold">Adicionar Fotos:</label>
                            <input type="file" name="fotos[]" class="form-control form-control-sm" multiple>
                            <button type="submit" name="update_equino" class="btn btn-sm btn-dark w-100 mt-2">
                                <i class="bi bi-upload"></i> Atualizar Dados / Fotos
                            </button>
                        </div>

                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">NOME DO ANIMAL</label>
                                    <input type="text" name="nome_animal" value="<?= $equino['nome_animal'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">RAÇA</label>
                                    <input type="text" name="raca" value="<?= $equino['raca'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">STATUS</label>
                                    <select name="status" class="form-control">
                                        <option value="ATIVO" <?= $equino['status'] == 'ATIVO' ? 'selected' : '' ?>>ATIVO</option>
                                        <option value="VENDIDO" <?= $equino['status'] == 'VENDIDO' ? 'selected' : '' ?>>VENDIDO</option>
                                        <option value="MORTE" <?= $equino['status'] == 'MORTE' ? 'selected' : '' ?>>MORTE</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">Nº REGISTRO</label>
                                    <input type="text" name="num_registro" value="<?= $equino['num_registro'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">PELAGEM</label>
                                    <input type="text" name="pelagem" value="<?= $equino['pelagem'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">PROPRIETÁRIO</label>
                                    <input type="text" name="proprietario" value="<?= $equino['proprietario'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">SEXO</label>
                                    <select name="sexo" class="form-control">
                                        <option value="Macho" <?= $equino['sexo'] == 'Macho' ? 'selected' : '' ?>>Macho</option>
                                        <option value="Fêmea" <?= $equino['sexo'] == 'Fêmea' ? 'selected' : '' ?>>Fêmea</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">DATA NASCIMENTO</label>
                                    <input type="date" name="data_nascimento" value="<?= $equino['data_nascimento'] ?>" class="form-control">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">LOCAL / PASTO</label>
                                    <input type="text" name="local" value="<?= $equino['local'] ?>" class="form-control">
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold">OBSERVAÇÕES GERAIS</label>
                                    <textarea name="descricao_geral" class="form-control" rows="2"><?= $equino['descricao_geral'] ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12"><h5 class="border-bottom pb-2"><i class="bi bi-images"></i> Galeria de Fotos</h5></div>
                        <?php
                        $fotos_query = mysqli_query($conn, "SELECT * FROM fotos_equinos WHERE cod_equino = '$id' ORDER BY capa DESC");
                        if(mysqli_num_rows($fotos_query) > 0){
                            while($foto = mysqli_fetch_array($fotos_query)):
                        ?>
                            <div class="col-md-2 mb-3 text-center">
                                <div class="card p-1 <?= $foto['capa'] ? 'border-primary shadow-sm' : '' ?>">
                                    <img src="uploads/imagens/<?= $foto['caminho'] ?>" class="foto-galeria">
                                    <div class="btn-group mt-1">
                                        <?php if(!$foto['capa']): ?>
                                            <a href="code_equino.php?definir_capa=<?= $foto['id_foto'] ?>&cod_equino=<?= $id ?>" class="btn btn-xs btn-outline-primary" title="Capa"><i class="bi bi-star"></i></a>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-xs btn-primary" disabled><i class="bi bi-star-fill"></i></button>
                                        <?php endif; ?>
                                        <a href="code_equino.php?excluir_foto=<?= $foto['id_foto'] ?>&cod_equino=<?= $id ?>" class="btn btn-xs btn-outline-danger" onclick="return confirm('Apagar foto?')"><i class="bi bi-trash"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; } else { echo "<div class='col-12'><p class='text-muted ps-3'>Nenhuma foto disponível.</p></div>"; } ?>
                    </div>

                    <div class="mt-3">
                        <button type="submit" name="update_equino" class="btn btn-success"><i class="bi bi-save"></i> Salvar Alterações</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalOcorrencia">
                            <i class="bi bi-plus-circle"></i> Registrar Ocorrência
                        </button>
                    </div>
                </form>

                <h5 class="mt-5 border-bottom pb-2 text-primary"><i class="bi bi-journal-text"></i> Histórico de Ocorrências</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped text-center align-middle">
    <thead class="table-dark">
        <tr>
            <th>DATA</th>
            <th>TIPO</th>
            <th>PESO</th>
            <th>VETERINÁRIO</th>
            <th>DESCRIÇÃO</th>
            <th>ANEXO</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $res_oc = mysqli_query($conn, "SELECT * FROM ocorrencias_equinos WHERE cod_equino = '$id' ORDER BY data_evento DESC");
        while($oc = mysqli_fetch_array($res_oc)):
        ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($oc['data_evento'])) ?></td>
                <td><?= $oc['tipo_evento'] ?></td>
                <td><?= ($oc['peso_kg'] > 0 ? $oc['peso_kg']." kg" : "-") ?></td>
                <td><?= $oc['veterinario'] ?></td>
                <td class="text-start"><?= $oc['descricao_detalhada'] ?></td>
                <td>
                    <?php if($oc['anexo']): ?>
                        <a href="uploads/documentos/<?= $oc['anexo'] ?>" target="_blank" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-file-earmark-text"></i> Ver
                        </a>
                    <?php else: ?>
                        <span class="text-muted small">Sem anexo</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
                </div>
            </div>
        </div>
    </div>

    <?php include('modal_ocorrencia.php'); ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>