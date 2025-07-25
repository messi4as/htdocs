<?php
require 'db_connect.php';

// Consulta para obter todos os protocolos, do mais recente para o mais antigo
$sql = "SELECT id, data_entrega, descricao, entregador, receptor, caminho_anexo FROM protocolos ORDER BY data_entrega DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Protocolos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h2 class="mb-0">Protocolos de Entrega</h2>
                <a href="protocolo_form.php" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Novo Protocolo
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success">Protocolo registrado com sucesso!</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger">Ocorreu um erro ao registrar o protocolo. Detalhe: <?= htmlspecialchars($_GET['msg']) ?></div>
                <?php endif; ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Protocolo</th>
                                <th>Data/Hora</th>
                                <th>Descrição</th>
                                <th>Entregue Por:</th>
                                <th>Recebido Por:</th>
                                <th class="text-center">Anexo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold"><?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($row['data_entrega'])) ?></td>
                                        <td><?= htmlspecialchars($row['descricao']) ?></td>
                                        <td><?= htmlspecialchars($row['entregador']) ?></td>
                                        <td><?= htmlspecialchars($row['receptor']) ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['caminho_anexo'])): ?>
                                                <a href="<?= htmlspecialchars($row['caminho_anexo']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-paperclip"></i> Ver Anexo
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Nenhum protocolo registrado ainda.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>