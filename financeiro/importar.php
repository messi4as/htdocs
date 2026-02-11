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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Adicionando Chart.js -->

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
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        <?php if (isset($_GET['import_success']) && $_GET['import_success'] == 'true'): ?>
            <div class="alert alert-success">
                Dados importados com sucesso! Linhas importadas: <?= htmlspecialchars($_GET['linhas_importadas']) ?>
            </div>
        <?php endif; ?>



        <div class="row">
            <div class="col-md-12">


                <div class="card mt-4">
                    <div class="card-header">
                        <h4>IMPORTAÇÃO DE DADOS FINANCEIROS</h4>
                    </div>

                    <div class="card-body">
                        <p class="text-muted"><b>Use este formulário para subir os dados das planilhas financeiras.</b></p>
                        <p class="small text-secondary">Colunas: <b>A:</b> Data (AAAA-MM-DD) | <b>B:</b> Descrição | <b>C:</b> Responsável | <b>D:</b> Valor | <b>E:</b> Forma de Pagamento | <b>F:</b> Tipo | <i style="color: blue;"><b>Arquivo EX: M2-JAN-DEZ-2022.xlsx</b></i></p>


                        <form action="importar_excel_financeiro.php" method="post" enctype="multipart/form-data">
                            <label class="form-label">Importar Planilha do Financeiro:</label>
                            <div class="row">
                                <div class="col-md-8">
                                    <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" name="importar_excel_financeiro" class="btn btn-danger me-2 form-control">Importar Financeiro</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>ATUALIZAÇÃO DE DADOS FINANCEIROS</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><b>Use este formulário para Atualizar os dados financeiros caso sejam necessários.</b></p>
                        <p class="small text-secondary">Colunas: <b>A:</b> Código_Financeiro | <b>B:</b> Data (AAAA-MM-DD) | <b>C:</b> Descrição | <b>D:</b> Responsável | <b>E:</b> Valor | <b>F:</b> Forma de Pagamento | <b>G:</b> Tipo | <i style="color: blue;"><b>Arquivo EX: M2-JAN-DEZ-2022_UPDATE.xlsx</b></i></p>





                        <form action="atualizar_financeiro.php" method="post" enctype="multipart/form-data">
                            <label class="form-label">Importar Planilha para Atualizar dados:</label>
                            <div class="row">

                                <div class="col-md-8">
                                    <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" name="atualizar_financeiro" class="btn btn-warning form-control">Atualizar Financeiro</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>ATUALIZAÇÃO DE ANEXOS</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><b>Use este formulário para atualizar os anexos de documentos.</b></p>
                        <p class="small text-secondary">Colunas: <b>A:</b> Código_Financeiro | <b>B:</b> Caminho Comprovantes | <b>C:</b> Caminho Documentos | <i style="color: blue;"><b>Arquivo EX: UPDATE M2-01-22.xlsx</b></i></p>




                        <form action="atualizar_documentos.php" method="post" enctype="multipart/form-data">
                            <label class="form-label">Importar Planilha para subir Documentos :</label>
                            <div class="row">

                                <div class="col-md-8">
                                    <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                </div>

                                <div class="col-md-4">
                                    <button type="submit" name="atualizar_documentos" class="btn btn-success form-control">Atualizar Documentos</button>
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