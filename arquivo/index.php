<?php 
include('db_connect.php'); 
include('/xampp/htdocs/navbar.php'); 

// 1. Busca as caixas e conta os volumes
$sql = "SELECT c.*, COUNT(v.id) as total_volumes 
        FROM caixas c 
        LEFT JOIN volumes_arquivos v ON c.id = v.caixa_id 
        WHERE c.id <= 144 
        GROUP BY c.id 
        ORDER BY c.armario, c.bandeja, c.posicao_na_bandeja";
$res = $conn->query($sql);

$caixas_organizadas = [];
$digitalizados = 0;      
$nao_digitalizados = 0;  
$livres = 0;             

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $caixas_organizadas[$row['armario']][$row['bandeja']][$row['posicao_na_bandeja']] = $row;
        
        $nome = trim($row['nome_caixa']);
        $nome_upper = mb_strtoupper($nome, 'UTF-8');
        $esta_livre = (empty($nome) || str_contains($nome_upper, 'DISPONÍVEL') || str_contains($nome_upper, 'DISPONIVEL'));

        if ($row['total_volumes'] > 0) {
            $digitalizados++;
        } elseif (!$esta_livre) {
            $nao_digitalizados++;
        } else {
            $livres++;
        }
    }
}

$total_fisico = 144;
$caixas_com_nome = $digitalizados + $nao_digitalizados;
$percentual = ($total_fisico > 0) ? round(($caixas_com_nome / $total_fisico) * 100) : 0;

$res_incin = $conn->query("SELECT COUNT(*) as total FROM volumes_arquivos WHERE caixa_id = 145");
$incinerados = $res_incin ? $res_incin->fetch_assoc()['total'] : 0;
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Arquivo Morto - Gestão Visual</title>
  <style>
    .shelf-row { border-bottom: 4px solid #8b4513; margin-bottom: 15px; padding-bottom: 5px; background: #fff; }
    
    /* Base da Caixa */
    .box-slot { 
        height: 100px !important; display: flex; align-items: center; justify-content: center; 
        position: relative; transition: 0.3s; border-radius: 4px; text-align: center; 
        padding: 8px !important; text-decoration: none; font-weight: bold; border: 2px solid transparent;
    }
    .box-text { font-size: 0.75rem; text-transform: uppercase; line-height: 1.2; }
    .box-slot:hover { transform: scale(1.03); filter: brightness(95%); z-index: 5; }

    /* ESTILOS POR STATUS (SUAVES) */
    
    /* 1. Digitalizados: Azul Claro / Fonte Azul Escuro */
    .status-digitalizado {
        background-color: #e3f2fd !important;
        color: #0d47a1 !important;
        border: 2px solid #bbdefb !important;
    }
    .status-digitalizado .badge-count { background: #0d47a1; }

    /* 2. Não Digitalizados: Amarelo Claro / Fonte Dourada/Marrom */
    .status-pendente {
        background-color: #fff9c4 !important;
        color: #827717 !important;
        border: 2px solid #fff59d !important;
    }

    /* 3. Livres: Verde Claro / Fonte Verde Escuro */
    .status-livre {
        background-color: #e8f5e9 !important;
        color: #1b5e20 !important;
        border: 2px dashed #c8e6c9 !important;
    }

    .badge-count { position: absolute; top: 2px; right: 2px; color: white; font-size: 0.6rem; padding: 1px 5px; border-radius: 3px; }

    /* Dashboard Estilo Texto Branco */
    .dashboard-card h6 { color: #ffffff !important; font-size: 0.7rem; font-weight: bold; opacity: 0.9; }
    .dashboard-card h3 { color: #ffffff !important; font-weight: 800; }

    .label-bandeja { min-width: 110px; font-weight: bold; font-size: 0.75rem; display: flex; align-items: center; justify-content: center; background: #343a40; color: white; border-radius: 4px 0 0 4px; }
    .nav-tabs .nav-link.active { border-bottom: 3px solid #ffc107 !important; font-weight: bold; color: #000; }
  </style>
</head>
<body class="bg-light">

<div class="container mt-4">
    <div class="row mb-4 text-center">
        <div class="col"><div class="card bg-primary shadow-sm border-0 dashboard-card"><div class="card-body"><h6>DIGITALIZADOS</h6><h3 class="mb-0"><?= $digitalizados ?></h3></div></div></div>
        <div class="col"><div class="card bg-warning shadow-sm border-0 dashboard-card"><div class="card-body"><h6>NÃO DIGITALIZADOS</h6><h3 class="mb-0"><?= $nao_digitalizados ?></h3></div></div></div>
        <div class="col"><div class="card bg-success shadow-sm border-0 dashboard-card"><div class="card-body"><h6>LIVRES</h6><h3 class="mb-0"><?= $livres ?></h3></div></div></div>
        <div class="col"><div class="card bg-dark shadow-sm border-0 dashboard-card"><div class="card-body"><h6>INCINERADOS</h6><h3 class="mb-0"><?= $incinerados ?></h3></div></div></div>
        <div class="col-md-3">
            <div class="card bg-white shadow-sm border-0"><div class="card-body"><h6 class="text-muted small fw-bold">OCUPAÇÃO DAS ESTANTES</h6>
            <div class="progress" style="height: 10px;"><div class="progress-bar bg-info" style="width: <?= $percentual ?>%"></div></div>
            <small class="fw-bold text-dark"><?= $percentual ?>% utilizada</small></div></div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col-md-3"><h4 class="mb-0 fw-bold"><i class="bi bi-archive-fill text-warning"></i> GESTÃO DE ARQUIVOS</h4></div>
                <div class="col-md-6 text-center">
                    <form action="busca.php" method="GET" class="d-inline-block w-100" style="max-width: 500px;">
                        <div class="input-group"><input type="text" name="q" class="form-control" placeholder="Buscar..."><button class="btn btn-warning fw-bold" type="submit">BUSCAR</button></div>
                    </form>
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group">
                        <a href="remanejamento.php" class="btn btn-outline-dark btn-sm fw-bold">REMANEJAR</a>
                        <a href="detalhes_caixa.php?id=145" class="btn btn-dark btn-sm fw-bold">INCINERADOS</a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <ul class="nav nav-tabs nav-fill mb-4">
                <?php for($a=1; $a<=4; $a++): ?>
                <li class="nav-item">
                    <button class="nav-link <?= ($a==1)?'active':'' ?>" data-bs-toggle="tab" data-bs-target="#armario-<?= $a ?>">ESTANTE <?= $a ?></button>
                </li>
                <?php endfor; ?>
            </ul>

            <div class="tab-content">
                <?php for($a=1; $a<=4; $a++): ?>
                <div class="tab-pane fade <?= ($a==1)?'show active':'' ?>" id="armario-<?= $a ?>">
                    <?php foreach(range('A', 'F') as $letra): ?>
                    <div class="d-flex shelf-row g-2">
                        <div class="label-bandeja shadow-sm me-2"><?= 'BANDEJA_' . $letra ?></div>
                        <div class="row g-2 flex-grow-1">
                            <?php for($p=1; $p<=6; $p++): 
                                $caixa = $caixas_organizadas[$a][$letra][$p] ?? null;
                                
                                // Lógica de Classe CSS por Status
                                $classe_status = 'status-livre'; 
                                if ($caixa) {
                                    $nome_upper = mb_strtoupper(trim($caixa['nome_caixa']), 'UTF-8');
                                    $tem_nome = (!empty($nome_upper) && !str_contains($nome_upper, 'DISPONÍVEL') && !str_contains($nome_upper, 'DISPONIVEL'));
                                    
                                    if ($caixa['total_volumes'] > 0) {
                                        $classe_status = 'status-digitalizado';
                                    } elseif ($tem_nome) {
                                        $classe_status = 'status-pendente';
                                    }
                                }
                            ?>
                                <div class="col">
                                    <?php if($caixa): ?>
                                        <a href="detalhes_caixa.php?id=<?= $caixa['id'] ?>&armario=<?= $a ?>" class="box-slot shadow-sm <?= $classe_status ?>">
                                            <?php if($caixa['total_volumes'] > 0): ?><span class="badge-count"><?= $caixa['total_volumes'] ?></span><?php endif; ?>
                                            <div class="box-text"><?= htmlspecialchars($caixa['nome_caixa']) ?></div>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
</div>

<script src="/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const urlParams = new URLSearchParams(window.location.search);
    const arm = urlParams.get('armario');
    if (arm) {
        const triggerEl = document.querySelector('button[data-bs-target="#armario-' + arm + '"]');
        if (triggerEl) bootstrap.Tab.getOrCreateInstance(triggerEl).show();
    }

    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabEl => {
        tabEl.addEventListener('shown.bs.tab', function (event) {
            const targetId = event.target.getAttribute('data-bs-target');
            const armNum = targetId.split('-')[1];
            window.history.replaceState(null, '', window.location.pathname + '?armario=' + armNum);
        });
    });
});
</script>
</body>
</html>