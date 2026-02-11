<?php 
include('db_connect.php'); 
include('/xampp/htdocs/navbar.php'); 

$busca       = $_GET['q'] ?? '';
$responsavel = $_GET['responsavel'] ?? '';

// Montagem da Query Dinâmica (Busca em caixas e volumes)
$sql = "SELECT c.id as caixa_id, c.nome_caixa, c.armario, c.bandeja, c.posicao_na_bandeja, 
               v.descricao as volume_nome, v.tipo_fonte, v.responsavel, v.data_inicio, v.data_fim
        FROM caixas c
        LEFT JOIN volumes_arquivos v ON c.id = v.caixa_id
        WHERE (c.nome_caixa LIKE '%$busca%' OR v.descricao LIKE '%$busca%')";

if (!empty($responsavel)) {
    $sql .= " AND v.responsavel = '$responsavel'";
}

$sql .= " ORDER BY c.nome_caixa ASC";
$res = $conn->query($sql);
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Busca no Arquivo - Lista de Resultados</title>
  <style>
      .badge-loc { font-size: 0.8rem; font-weight: 500; }
  </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="bi bi-search text-warning"></i> Resultados da Pesquisa</h3>
        <a href="index.php" class="btn btn-secondary shadow-sm">
            <i class="bi bi-grid-3x3-gap"></i> Voltar para Estante Visual
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <p class="mb-0 text-muted">A pesquisar por: <strong>"<?= htmlspecialchars($busca) ?>"</strong></p>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 250px;">Localização Física</th>
                            <th>Caixa</th>
                            <th>Conteúdo / Documento</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res && $res->num_rows > 0): ?>
                            <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-dark badge-loc">Arm. <?= $row['armario'] ?></span>
                                    <span class="badge bg-dark badge-loc">Band. <?= $row['bandeja'] ?></span>
                                    <span class="badge bg-secondary badge-loc text-dark" style="background-color: #e9ecef;">Pos. <?= $row['posicao_na_bandeja'] ?></span>
                                </td>
                                <td class="fw-bold text-uppercase"><?= htmlspecialchars($row['nome_caixa']) ?></td>
                                <td>
                                    <?php if($row['volume_nome']): ?>
                                        <div class="fw-semibold"><?= htmlspecialchars($row['volume_nome']) ?></div>
                                        <small class="text-muted"><i class="bi bi-person"></i> <?= $row['responsavel'] ?></small>
                                    <?php else: ?>
                                        <span class="text-muted italic">Caixa sem documentos lançados</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <a href="detalhes_caixa.php?id=<?= $row['caixa_id'] ?>&armario=<?= $row['armario'] ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver na Estante
                                    </a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-info-circle display-4"></i><br>
                                    Nenhum resultado encontrado para esta busca.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
</body>
</html>