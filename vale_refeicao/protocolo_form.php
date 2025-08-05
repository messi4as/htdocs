<?php
// Define o fuso horário para a sua localização para garantir que a hora esteja correta
date_default_timezone_set('America/Sao_Paulo');
// Formata a data e hora atuais para o formato que o input 'datetime-local' aceita
$data_hora_atual = date('Y-m-d\TH:i');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Registrar Novo Protocolo</title>
</head>
<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h2>Registrar Novo Protocolo de Entrega</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                    <div class="alert alert-success">Protocolo registrado com sucesso!</div>
                <?php elseif (isset($_GET['status']) && $_GET['status'] == 'error'): ?>
                    <div class="alert alert-danger">Ocorreu um erro ao registrar o protocolo. Detalhe: <?= htmlspecialchars($_GET['msg']) ?></div>
                <?php endif; ?>

                <form action="salvar_protocolo.php" method="POST" enctype="multipart/form-data">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="descricao" class="form-label">Descrição</label>
                            <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Ex: Entrega de 10 cartões de alimentação referentes ao mês de Julho."></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="data_entrega" class="form-label">Data e Hora da Entrega</label>
                            <input type="datetime-local" class="form-control" id="data_entrega" name="data_entrega" value="<?= $data_hora_atual ?>" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="entregador" class="form-label">Nome do Entregador</label>
                            <input type="text" class="form-control" id="entregador" name="entregador" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="receptor" class="form-label">Nome do Receptor</label>
                            <input type="text" class="form-control" id="receptor" name="receptor" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="anexo" class="form-label">Anexo (Documento Assinado)</label>
                        <input class="form-control" type="file" id="anexo" name="anexo" accept="application/pdf,image/*">
                        <div class="form-text">Envie um arquivo PDF ou uma imagem do documento.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Registrar Protocolo
                    </button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>