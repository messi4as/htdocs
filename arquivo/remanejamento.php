<?php 
include('db_connect.php'); 
include('/xampp/htdocs/navbar.php'); 

$msg = "";
$tipo_msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['btn_atualizar_vaga'])) {
    $id_origem = $_POST['id_caixa'];
    $novo_armario = $_POST['armario'];
    $nova_bandeja = $_POST['bandeja'];
    $nova_posicao = $_POST['posicao'];
    $novo_nome    = $_POST['nome_caixa'];

    // 1. Busca quem está no destino
    $sql_destino = "SELECT id FROM caixas WHERE armario = ? AND bandeja = ? AND posicao_na_bandeja = ? LIMIT 1";
    $stmt_dest = $conn->prepare($sql_destino);
    $stmt_dest->bind_param("isi", $novo_armario, $nova_bandeja, $nova_posicao);
    $stmt_dest->execute();
    $caixa_destino = $stmt_dest->get_result()->fetch_assoc();

    // 2. Busca a posição atual da caixa de origem
    $sql_origem = "SELECT armario, bandeja, posicao_na_bandeja FROM caixas WHERE id = ?";
    $stmt_orig = $conn->prepare($sql_origem);
    $stmt_orig->bind_param("i", $id_origem);
    $stmt_orig->execute();
    $caixa_origem = $stmt_orig->get_result()->fetch_assoc();

    $conn->begin_transaction();
    try {
        if ($caixa_destino && $caixa_destino['id'] != $id_origem) {
            // PASSO A: Movemos a caixa que está no destino para uma "vaga fantasma" (temporária)
            // Isso evita o erro de Duplicate Entry
            $temp_pos = 999; 
            $sql_temp = "UPDATE caixas SET posicao_na_bandeja = ? WHERE id = ?";
            $stmt_temp = $conn->prepare($sql_temp);
            $stmt_temp->bind_param("ii", $temp_pos, $caixa_destino['id']);
            $stmt_temp->execute();

            // PASSO B: Movemos a sua caixa atual para o destino real
            $sql_move = "UPDATE caixas SET nome_caixa = ?, armario = ?, bandeja = ?, posicao_na_bandeja = ? WHERE id = ?";
            $stmt_move = $conn->prepare($sql_move);
            $stmt_move->bind_param("sisii", $novo_nome, $novo_armario, $nova_bandeja, $nova_posicao, $id_origem);
            $stmt_move->execute();

            // PASSO C: Movemos a caixa que estava no destino para a vaga que sobrou na origem
            $sql_swap = "UPDATE caixas SET armario = ?, bandeja = ?, posicao_na_bandeja = ? WHERE id = ?";
            $stmt_swap = $conn->prepare($sql_swap);
            $stmt_swap->bind_param("isii", $caixa_origem['armario'], $caixa_origem['bandeja'], $caixa_origem['posicao_na_bandeja'], $caixa_destino['id']);
            $stmt_swap->execute();

            $msg = "Troca inteligente realizada! As caixas inverteram de lugar.";
        } else {
            // Movimentação simples (vaga livre)
            $sql_up = "UPDATE caixas SET nome_caixa = ?, armario = ?, bandeja = ?, posicao_na_bandeja = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_up);
            $stmt->bind_param("sisii", $novo_nome, $novo_armario, $nova_bandeja, $nova_posicao, $id_origem);
            $stmt->execute();
            $msg = "Posição atualizada com sucesso!";
        }

        $conn->commit();
        $tipo_msg = "success";
    } catch (Exception $e) {
        $conn->rollback();
        $msg = "Erro técnico: " . $e->getMessage();
        $tipo_msg = "danger";
    }
}

$sql = "SELECT * FROM caixas WHERE id <= 144 ORDER BY armario, bandeja, posicao_na_bandeja";
$res = $conn->query($sql);
?>

<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Remanejamento - Arquivo</title>
  <style>
    .table thead th { background-color: #212529; color: white; border: none; text-transform: uppercase; font-size: 0.85rem; }
    .badge-loc { font-family: monospace; font-size: 0.9rem; padding: 6px 10px; }
    .btn-trocar { background-color: #0d6efd; color: white; font-weight: bold; }
  </style>
</head>
<body class="bg-light">
<div class="container mt-4 mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold"><i class="bi bi-arrow-left-right text-primary"></i> Troca de Lugares</h3>
            <span class="text-muted">Inverta caixas de posição sem erros de duplicidade.</span>
        </div>
        <a href="index.php" class="btn btn-dark shadow-sm">Voltar à Estante</a>
    </div>

    <?php if($msg): ?>
        <div class="alert alert-<?= $tipo_msg ?> alert-dismissible fade show shadow-sm" role="alert">
            <?= $msg ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 15%;">Endereço</th>
                            <th style="width: 45%;">Nome da Caixa</th>
                            <th style="width: 25%;">Trocar para...</th>
                            <th class="text-center" style="width: 15%;">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = $res->fetch_assoc()): 
                            $is_vazia = (stripos($row['nome_caixa'], 'DISPONÍVEL') !== false);
                        ?>
                        <tr>
                            <td class="ps-3">
                                <span class="badge bg-white text-dark border badge-loc">
                                    ESTANTE:<?= $row['armario'] ?> BANDEJA:<?= $row['bandeja'] ?> POSIÇÃO:<?= $row['posicao_na_bandeja'] ?>
                                </span>
                            </td>
                            <td>
                                <form action="remanejamento.php" method="POST" class="d-flex m-0">
                                    <input type="hidden" name="id_caixa" value="<?= $row['id'] ?>">
                                    <input type="text" name="nome_caixa" class="form-control form-control-sm <?= $is_vazia ? 'text-muted' : 'fw-bold' ?>" value="<?= htmlspecialchars($row['nome_caixa']) ?>">
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <select name="armario" class="form-select form-select-sm">
                                        <?php for($i=1;$i<=4;$i++) echo "<option value='$i' ".($row['armario']==$i?'selected':'').">$i</option>"; ?>
                                    </select>
                                    <select name="bandeja" class="form-select form-select-sm">
                                        <?php foreach(range('A','F') as $l) echo "<option value='$l' ".($row['bandeja']==$l?'selected':'').">$l</option>"; ?>
                                    </select>
                                    <select name="posicao" class="form-select form-select-sm">
                                        <?php for($i=1;$i<=6;$i++) echo "<option value='$i' ".($row['posicao_na_bandeja']==$i?'selected':'').">$i</option>"; ?>
                                    </select>
                                </div>
                            </td>
                            <td class="text-center">
                                <button type="submit" name="btn_atualizar_vaga" class="btn btn-trocar btn-sm w-100">Trocar</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>