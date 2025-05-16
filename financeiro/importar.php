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
        .form-container {
            display: flex;
            flex-direction: row;
            gap: 20px;
            display: flex;
            flex-direction: column;
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
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
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>IMPORTAÇÃO DE DADOS FINANCEIROS</h4>

                               

                                <br>

                            </h4>


                          
                            
                                        <form action="importar_excel_financeiro.php" method="post" enctype="multipart/form-data">                                         
                                            <label class="form-label">Importar Planilha do Financeiro:</label>
                                        <div class="row">  
                                        
                                        <div class="col-md-5">                                      
                                            <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit" name="importar_excel_financeiro" class="btn btn-danger me-2 form-control">Importar Financeiro</button>
                                        </div>                                        
                                        </form> 
                                        
                                        
                                              <form action="atualizar_financeiro.php" method="post" enctype="multipart/form-data">
                                            <label class="form-label">Importar Planilha para Atualizar dados:</label>
                                        <div class="row">

                                        <div class="col-md-5">
                                            <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit" name="atualizar_financeiro" class="btn btn-warning form-control">Atualizar Financeiro</button>
                                        </div>
                                        </form>
                            
                          

                                        <form action="atualizar_documentos.php" method="post" enctype="multipart/form-data">
                                            <label class="form-label">Importar Planilha para subir Documentos :</label>
                                        <div class="row">

                                        <div class="col-md-5">
                                            <input type="file" name="arquivo_excel" class="form-control" accept=".xlsx, .xls" required>
                                        </div>

                                        <div class="col-md-2">
                                            <button type="submit" name="atualizar_documentos" class="btn btn-success form-control">Atualizar Documentos</button>
                                        </div>
                                        </form>



                                        
                                  

                            



                            

                            </div>

                        </div>

                            </div>
</body>

</html>