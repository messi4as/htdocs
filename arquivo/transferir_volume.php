<?php 
include('db_connect.php'); 
include('/xampp/htdocs/navbar.php'); 

$volume_id = $_GET['id'] ?? null;
$caixa_origem_id = $_GET['caixa_id'] ?? null;

if (!$volume_id) {
    die("ID do volume não fornecido.");
}

// 1. BUSCA DADOS DO VOLUME E VERIFICA SE ESTÁ NO REPOSITÓRIO 145
$stmt = $conn->prepare("SELECT * FROM volumes_arquivos WHERE id = ?");
$stmt->bind_param("i", $volume_id);
$stmt->execute();
$vol = $stmt->get_result()->fetch_assoc();

if (!$vol) {
    die("Volume não encontrado.");
}

// TRAVA DE SEGURANÇA: Impede transferência de volumes já incinerados
if ($vol['caixa_id'] == 145) {
    echo "<script>
            alert('ACESSO NEGADO: Este volume já foi incinerado e é um registro digital permanente. Não pode ser movido para caixas físicas.');
            window.location.href='detalhes_caixa.php?id=145';
          </script>";
    exit;
}

// 2. BUSCA TODAS AS CAIXAS DISPONÍVEIS PARA DESTINO (1 a 145)
// Ordenamos para que o Repositório 145 apareça no topo ou no fim da lista
$caixas = $conn->query("SELECT id, nome_caixa, armario, bandeja, posicao_na_bandeja FROM caixas ORDER BY id ASC");
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Transferir Volume</title>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Mover para Outra Caixa</h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info py-2 small">
                        <strong>Volume:</strong> <?= htmlspecialchars($vol['descricao']) ?><br>
                        <strong>Local Atual:</strong> Caixa ID <?= $vol['caixa_id'] ?>
                    </div>

                    <form action="processar_transferencia.php" method="POST">
                        <input type="hidden" name="volume_id" value="<?= $volume_id ?>">
                        <input type="hidden" name="caixa_origem_id" value="<?= $vol['caixa_id'] ?>">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary">Selecione o Destino:</label>
                            <select name="nova_caixa_id" class="form-select form-select-lg" required>
                                <option value="" disabled selected>Escolha a nova caixa...</option>
                                <?php while($c = $caixas->fetch_assoc()): ?>
                                    <option value="<?= $c['id'] ?>" <?= ($c['id'] == $vol['caixa_id']) ? 'disabled' : '' ?>>
                                        ID <?= $c['id'] ?> - <?= htmlspecialchars($c['nome_caixa']) ?> 
                                        <?= ($c['id'] == 145) ? ' (PROCESSO DE INCINERAÇÃO)' : '' ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="form-text text-danger mt-2">
                                <i class="bi bi-exclamation-triangle"></i> Atenção: Ao mover para o <strong>Repositório 145</strong>, a ação será permanente.
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <a href="detalhes_caixa.php?id=<?= $vol['caixa_id'] ?>" class="btn btn-light border">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm" onclick="return confirm('Confirmar transferência de local?')">
                                Confirmar Movimentação
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>