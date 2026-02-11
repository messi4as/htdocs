<?php 
include('db_connect.php'); 
include('/xampp/htdocs/navbar.php'); 

$id = $_GET['id'] ?? null;

if (!$id) {
    header("Location: index.php");
    exit;
}

// 1. Busca os dados da caixa atual
$sql_caixa = "SELECT * FROM caixas WHERE id = $id";
$res_caixa = $conn->query($sql_caixa);
$caixa = $res_caixa->fetch_assoc();

$armario_destino = $caixa['armario'];

// 2. Busca os volumes arquivados
$sql_volumes = "SELECT * FROM volumes_arquivos WHERE caixa_id = $id ORDER BY data_cadastro DESC";
$res_volumes = $conn->query($sql_volumes);
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  
  <title>Arquivo - <?= htmlspecialchars($caixa['nome_caixa']) ?></title>
  <style>
      .card-header-custom { background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; font-weight: bold; }
      .btn-custom-gold { background-color: rgb(175, 166, 118); color: black; font-weight: bold; border: none; }
      
      /* Ajustes de visibilidade do Select2 */
      .select2-container { width: 100% !important; z-index: 9999 !important; }
      .select2-results__option { color: #000 !important; background-color: #fff !important; }
      .select2-dropdown { z-index: 10000 !important; background-color: white !important; border: 1px solid #aaa !important; }
      .select2-selection--single { height: 38px !important; display: flex !important; align-items: center; border: 1px solid #dee2e6 !important; }
  </style>
</head>
<body class="bg-light">

<div class="container mt-4 pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="index.php?armario=<?= $armario_destino ?>#armario-<?= $armario_destino ?>" class="btn btn-outline-secondary shadow-sm">
            <i class="bi bi-arrow-left"></i> Voltar a Estante <?= $armario_destino ?>
        </a>
        <h3 class="fw-bold mb-0 text-uppercase text-primary"><i class="bi bi-archive"></i> <?= htmlspecialchars($caixa['nome_caixa']) ?></h3>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header card-header-custom text-center">ENDEREÇO NA ESTANTE</div>
                <div class="card-body">
                    <form action="atualizar_caixa_completo.php" method="POST">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">NOME DA CAIXA</label>
                            <input type="text" name="nome_caixa" class="form-control fw-bold text-uppercase" value="<?= htmlspecialchars($caixa['nome_caixa']) ?>">
                        </div>
                        <div class="row g-2 text-center">
                            <div class="col-4"> <label class="small fw-bold">ESTANTE</label><input type="text" class="form-control text-center bg-light" value="<?= $caixa['armario'] ?>" readonly> </div>
                            <div class="col-4"> <label class="small fw-bold">BANDEJA</label><input type="text" class="form-control text-center bg-light" value="<?= $caixa['bandeja'] ?>" readonly> </div>
                            <div class="col-4"> <label class="small fw-bold">POSIÇÃO</label><input type="text" class="form-control text-center bg-light" value="<?= $caixa['posicao_na_bandeja'] ?>" readonly> </div>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-dark shadow-sm">SALVAR NOME</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header card-header-custom d-flex justify-content-between align-items-center bg-white">
                    <span><i class="bi bi-collection"></i> ITENS ARQUIVADOS</span>
                    <span class="badge bg-primary"><?= $res_volumes->num_rows ?> Itens</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr><th class="ps-3">DESCRIÇÃO</th><th>RESPONSÁVEL</th><th class="text-end pe-3">AÇÕES</th></tr>
                        </thead>
                        <tbody>
                            <?php while($vol = $res_volumes->fetch_assoc()): 
                                $host_atual = $_SERVER['HTTP_HOST'];
                                $linkFinanceiro = "http://" . $host_atual . "/financeiro/index.php?cod_financeiro=&data_inicio=" . $vol['data_inicio'] . "&data_fim=" . $vol['data_fim'] . "&responsavel=" . urlencode($vol['responsavel']);
                            ?>
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-uppercase small"><?= htmlspecialchars($vol['descricao']) ?></div>
                                </td>
                                <td class="small fw-bold text-secondary"><?= htmlspecialchars($vol['responsavel']) ?></td>
                                <td class="text-end pe-3">
                                    <div class="btn-group">
                                        <?php if($vol['tipo_fonte'] == 'link'): ?>
                                            <a href="<?= $linkFinanceiro ?>" target="_blank" class="btn btn-sm btn-primary"><i class="bi bi-link-45deg"></i></a>
                                        <?php else: ?>
                                            <a href="<?= $vol['caminho_anexo'] ?>" target="_blank" class="btn btn-sm btn-danger"><i class="bi bi-file-earmark-pdf"></i></a>
                                        <?php endif; ?>
                                        
                                        <button type="button" class="btn btn-sm btn-custom-gold" onclick="abrirTransferencia(<?= $vol['id'] ?>, '<?= htmlspecialchars($vol['descricao']) ?>')">
                                            <i class="bi bi-arrow-left-right"></i>
                                        </button>
                                    </div>
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

<div class="modal fade" id="modalTransferir" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="processar_transferencia.php" method="POST">
        <div class="modal-header bg-warning">
          <h5 class="modal-title fw-bold">TRANSFERIR PARA OUTRA CAIXA</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <p>Mover: <strong id="txt_vol"></strong></p>
            <input type="hidden" name="volume_id" id="id_vol_transf">
            <input type="hidden" name="caixa_origem_id" value="<?= $id ?>">
            
            <div class="mb-3">
                <label class="form-label fw-bold small">CAIXA DE DESTINO:</label>
                <select name="nova_caixa_id" id="select_caixa_destino" class="form-control" required style="width: 100%;">
                    <option value=""></option>
                    <?php 
                    // Filtro para ignorar caixas sem nome
                    $sql_caixas = "SELECT id, nome_caixa FROM caixas 
                                   WHERE nome_caixa IS NOT NULL AND TRIM(nome_caixa) <> '' 
                                   ORDER BY nome_caixa ASC";
                    $cx = $conn->query($sql_caixas);
                    while($c = $cx->fetch_assoc()) {
                        echo "<option value='{$c['id']}'>" . htmlspecialchars($c['nome_caixa']) . "</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-warning w-100 fw-bold">CONFIRMAR TRANSFERÊNCIA</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
function abrirTransferencia(id, nome) {
    document.getElementById('id_vol_transf').value = id;
    document.getElementById('txt_vol').innerText = nome;
    var myModal = new bootstrap.Modal(document.getElementById('modalTransferir'));
    myModal.show();
}

$(document).ready(function() {
    // Inicialização segura do Select2
    $('#modalTransferir').on('shown.bs.modal', function () {
        $('#select_caixa_destino').select2({
            dropdownParent: $('#modalTransferir'),
            placeholder: "Pesquise a caixa...",
            allowClear: true
        });
    });

    $('#modalTransferir').on('hidden.bs.modal', function () {
        if ($('#select_caixa_destino').data('select2')) {
            $('#select_caixa_destino').select2('destroy');
        }
    });
});
</script>
</body>
</html>