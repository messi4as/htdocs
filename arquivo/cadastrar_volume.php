<?php 
include('db_connect.php');
include('/xampp/htdocs/navbar.php'); // Mantendo seu padrão de inclusão

$caixa_id = $_GET['caixa_id'] ?? null;
?>

<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Adicionar Volume</title>
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h4><i class="bi bi-file-earmark-pdf"></i> Adicionar Volume à Caixa</h4>
        </div>
        <div class="card-body">
            <form action="salvar_volume.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="caixa_id" value="<?php echo $caixa_id; ?>">
                
                <div class="mb-3">
                    <label class="form-label">Descrição do Conteúdo (Volume)</label>
                    <input type="text" name="descricao" class="form-control" placeholder="Ex: Notas Fiscais - Janeiro" required>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Situação</label>
                        <select name="situacao" class="form-select">
                            <option value="Pendente">Pendente</option>
                            <option value="Digitalizado">Digitalizado</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Arquivo PDF</label>
                        <input type="file" name="anexo" class="form-control" accept="application/pdf" required>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-success">Salvar Volume</button>
                    <a href="visualizar_caixas.php" class="btn btn-secondary">Finalizar</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>