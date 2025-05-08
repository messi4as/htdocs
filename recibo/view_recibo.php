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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="js/jquery.mask.min.js"></script>

    <title>VISUALIZAR RECIBO</title>

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
                            <h4>VISUALIZAR RECIBO
                                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['id'])) {
                                $codigo_recibo = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM recibo, emitente, emissor WHERE cod_recibo='$codigo_recibo' 
                            and recibo.cod_emitente = emitente.cod_emitente and recibo.cod_emissor = emissor.cod_emissor";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $codigo_recibo = mysqli_fetch_array($query);

                                    // Formata o código do recibo
                                    $codigo = str_pad($codigo_recibo['cod_recibo'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);

                            ?>

                                    <style>
                                        .button-container {
                                            display: flex;
                                            gap: 10px;
                                            /* Espaçamento entre os botões */
                                            flex-wrap: wrap;
                                            /* Permite que os botões quebrem linha se necessário */
                                            justify-content: space-between;
                                            /* Adicionado */
                                        }

                                        .btn-pj-pj {
                                            background-color: rgb(172, 136, 62);
                                            /* Verde Claro */
                                            color: black;
                                        }

                                        .btn-pj-pf {
                                            background-color: rgb(172, 136, 72);
                                            /* Cinza Escuro */
                                            color: black;
                                        }

                                        .btn-pf-pf {
                                            background-color: rgb(172, 136, 82);
                                            /* Cinza Claro */
                                            color: black;
                                        }

                                        .btn-pf-pj {
                                            background-color: rgb(172, 136, 92);
                                            /* Cinza Escuro */
                                            color: black;
                                        }


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

                                    <div class="form-group">
                                        <label class="form-label">&nbsp;CÓDIGO DO RECIBO:</label>
                                        <a class="form-control" style="width:75px ; background-color: #e9ecef;"><?= $cod_formatado ?></a>
                                    </div>

                                    <br>


                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;PRESTADOR_DE_SERVIÇO:</label>
                                            <a class="form-control"><?= $codigo_recibo['nome_emitente']; ?></a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;SÓCIO_/_REPRESENTANTE:</label>
                                            <a class="form-control"><?= $codigo_recibo['nome_emissor']; ?></a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">DATA:</label>
                                            <a class="form-control"><?= date('d/m/Y', strtotime($codigo_recibo['data_recibo'])) ?></a>

                                            <label class="form-label">&nbsp;LOCAL: </label>
                                            <a class="form-control"><?= $codigo_recibo['local_recibo']; ?></a>

                                            <label class="form-label">&nbsp;VALOR:</label>
                                            <a class="form-control"><?= $codigo_recibo['valor_recibo']; ?></a>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">VALOR_POR_EXTENSO:</label>
                                            <a class="form-control"><?= $codigo_recibo['valor_ext_recibo']; ?></a>
                                        </div>
                                    </div>
                                    <br>


                                    <label class="form-label">DESCRIÇÃO:</label>
                                    <div class="form-control" style="background-color: #e9ecef;"><?= $codigo_recibo['descricao_recibo']; ?></div>


                                    <br>
                                    <!-- Contêiner para alinhar os botões -->
                                    <div class="button-container">
                                        <form action="reportrecibo_pj_pj.php" method="get" target="_blank">
                                            <input type="hidden" name="id_recibo" value="<?php echo $codigo_recibo['cod_recibo']; ?>">
                                            <button type="submit" class="btn btn-pj-pj" style="width:250px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Recibo PJ-M2</button>
                                        </form>


                                        <form action="reportrecibo_pj_pf.php" method="get" target="_blank">
                                            <input type="hidden" name="id_recibo" value="<?php echo $codigo_recibo['cod_recibo']; ?>">
                                            <button type="submit" class="btn btn-pj-pf" style="width:300px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Recibo PJ-MAIARA/MARAISA</button>
                                        </form>

                                        <form action="reportrecibo_pf_pf.php" method="get" target="_blank">
                                            <input type="hidden" name="id_recibo" value="<?php echo $codigo_recibo['cod_recibo']; ?>">
                                            <button type="submit" class="btn btn-pf-pf" style="width:300px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Recibo PF-MAIARA/MARAISA</button>
                                        </form>


                                        <form action="reportrecibo_pf_pj.php" method="get" target="_blank">
                                            <input type="hidden" name="id_recibo" value="<?php echo $codigo_recibo['cod_recibo']; ?>">
                                            <button type="submit" class="btn btn-pf-pj" style="width:250px;height:50px;"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Recibo PF-M2</button>
                                        </form>


                                    </div>

                            <?php
                                } else {
                                    echo "<h5>Nenhum Recibo encontrado</h5>";
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