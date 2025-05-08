<?php
session_start();
require 'db_connect.php';
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>Ver Ordem de Serviço</title>

</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>DETALHE DA ORDEM DE SERVIÇO
                                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['id'])) {
                                $os_codigo = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM ordem_servico WHERE codigo='$os_codigo'";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $os_codigo = mysqli_fetch_array($query);

                                    // Formata o código do recibo
                                    $codigo = str_pad($os_codigo['codigo'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);
                            ?>
                                    <style>
                                        .form-container {
                                            display: flex;
                                            flex-wrap: wrap;
                                            gap: 20px;
                                            justify-content: space-between;
                                            /* Adicionado */
                                        }

                                        .form-group {
                                            display: flex;
                                            align-items: center;
                                            flex: 1;
                                            /* Adicionado */
                                            margin: 0 10px;
                                            /* Adicionado */
                                        }

                                        .form-label {
                                            font-weight: bold;
                                            margin-right: 10px;
                                        }

                                        input[type="text"],
                                        textarea,
                                        select {
                                            text-transform: none;
                                            width: 100%;
                                            /* Adicionado */
                                        }

                                        .btn {
                                            flex: 0 0 auto;
                                            /* Adicionado */
                                            margin-left: 10px;
                                            /* Adicionado */
                                        }

                                        .button-container {
                                            display: flex;
                                            gap: 10px;
                                            /* Espaçamento entre os botões */
                                            flex-wrap: wrap;
                                            /* Permite que os botões quebrem linha se necessário */
                                            justify-content: space-between;
                                            /* Adicionado */
                                        }

                                        .btn-maiara {
                                            background-color: rgb(263, 242, 205);
                                            /* Amarelo */
                                            color: black;
                                        }

                                        .btn-maraisa {
                                            background-color: rgb(166, 202, 236);
                                            /* Azul */
                                            color: black;
                                        }

                                        .btn-m2 {
                                            background-color: rgb(242, 242, 242);
                                            /* Cinza */
                                            color: black;
                                        }

                                        .btn-fazenda {
                                            background-color: rgb(252, 114, 206);
                                            /* Rosa */
                                            color: black;
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

                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">CÓDIGO</label>
                                            <a class="form-control" style="width:75px ; background-color: #e9ecef;">
                                                <?= $cod_formatado ?>
                                            </a>

                                            <label class="form-label">&nbsp;NOME</label>
                                            <a class="form-control">
                                                <?= mb_strtoupper($os_codigo['nome']); ?>
                                            </a>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">CPF:</label>
                                            <a class="form-control">
                                                <?= $os_codigo['cpf']; ?>
                                            </a>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">CNPJ:</label>
                                            <a class="form-control">
                                                <?= $os_codigo['cnpj']; ?>
                                            </a>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">CELULAR:</label>
                                            <a class="form-control">
                                                <?= $os_codigo['celular']; ?>
                                            </a>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">TELEFONE_FIXO:</label>
                                            <a class="form-control">
                                                <?= $os_codigo['telefone_fixo']; ?>
                                            </a>
                                        </div>
                                    </div>
                                    <br>

                                    <label class="form-label">ENDEREÇO:</label>
                                    <a class="form-control">
                                        <?= mb_strtoupper($os_codigo['endereco']); ?>
                                    </a>
                                    <br>

                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">CIDADE:</label>
                                            <a class="form-control">
                                                <?= mb_strtoupper($os_codigo['cidade']); ?>
                                            </a>

                                            <label class="form-label">&nbsp;CEP:</label>
                                            <a class="form-control">
                                                <?= $os_codigo['cep']; ?>
                                            </a>

                                            <label class="form-label">&nbsp;DATA:</label>
                                            <a class="form-control">
                                                <?= date('d/m/Y', strtotime($os_codigo['data'])) ?>
                                            </a>

                                            <label class="form-label">&nbsp;VALOR:</label>
                                            <a class="form-control">
                                                <?= $os_codigo['valor']; ?>
                                            </a>
                                        </div>
                                    </div>
                                    <br>

                                    <label class="form-label">DESCRIÇÃO:</label>
                                    <div class="form-control" style="background-color: #e9ecef;"><?= $os_codigo['descricao']; ?></div>

                                    <label class="form-label">FORMA DE PAGAMENTO:</label>
                                    <div class="form-control" style="background-color: #e9ecef;"><?= $os_codigo['forma_pagamento']; ?></div>


                                    <br>

                                    <!-- Contêiner para alinhar os botões -->
                                    <div class="button-container">
                                        <form action="reportmaiara.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?php echo $os_codigo['codigo']; ?>">
                                            <button type="submit" class="btn btn-maiara" style="width:250px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Gerar Relatório Maiara</button>
                                        </form>

                                        <form action="reportmaraisa.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?php echo $os_codigo['codigo']; ?>">
                                            <button type="submit" class="btn btn-maraisa" style="width:250px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Gerar Relatório Maraisa</button>
                                        </form>

                                        <form action="reportm2.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?php echo $os_codigo['codigo']; ?>">
                                            <button type="submit" class="btn btn-m2" style="width:250px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Gerar Relatório M2</button>
                                        </form>

                                        <form action="reportfazenda.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?php echo $os_codigo['codigo']; ?>">
                                            <button type="submit" class="btn btn-fazenda" style="width:250px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Gerar Relatório Fazenda</button>
                                        </form>
                                    </div>
                            <?php
                                } else {
                                    echo "<h5>Ordem de Serviço não encontrada</h5>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



</body>

</html>