<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <title>Importação Excel</title>
    <style>
        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .card {
            margin-bottom: 20px;
        }
    </style>
</head>


<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>

        <?php if (isset($_GET['import_success']) && $_GET['import_success'] == 'true'): ?>
            <div class="alert alert-success">
                Dados importados com sucesso! Linhas importadas: <?= htmlspecialchars($_GET['linhas_importadas']) ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-12">



                <div class="card mt-4">
                    <div class="card-header">
                        <h4>IMPORTAR NOVAS OCORRÊNCIAS (INDIVIDUALIZADO)</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted"><b>Use este formulário para criar novas ocorrências com pesos diferentes para cada animal.</b></p>
                        <p class="small text-secondary">Colunas: <b>A:</b> Brinco | <b>B:</b> Data (AAAA-MM-DD) | <b>C:</b> Local | <b>D:</b> Tipo | <b>E:</b> Peso | <b>F:</b> Descrição | <i style="color: blue;"><b>Arquivo: Importar Novas Ocorrências.xlsx</b></i></p>

                        <form action="importar_ocorrencias_excel.php" method="post" enctype="multipart/form-data">
                            <label class="form-label">Importar Planilha para criar novas Ocorrências:</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" name="importar_ocorrencias" class="btn btn-success w-100">
                                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Importar Novas Ocorrências
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>ATUALIZAÇÃO DE PESOS (OCORRÊNCIAS EXISTENTES)</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><b>Use este formulário para atualizar as ocorrências com pesos diferentes para cada animal.</b></p>
                        <p class="small text-secondary">Colunas: <b>A:</b> ID_Ocorrência | <b>B:</b> Peso | <i style="color: blue;"><b>Arquivo: Atualizar Pesos.xlsx</b></i></p>
                        <form action="atualizar_peso.php" method="post" enctype="multipart/form-data">
                            <label class="form-label">Importar Planilha para atualizar pesos de IDs existentes:</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" name="atualizar_peso" class="btn btn-warning w-100">
                                        <i class="bi bi-file-earmark-spreadsheet-fill"></i> Atualizar Pesos
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</body>

</html>