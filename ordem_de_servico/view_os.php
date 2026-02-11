<?php
session_start();
require 'db_connect.php';
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="/js/jquery.mask.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>Ver Ordem de Serviço</title>

    <style>
        /* Estilo para simular o campo de input desabilitado, mas legível */
        .view-field {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            display: block;
            width: 100%;
            min-height: 38px;
            color: #212529;
            word-wrap: break-word;
        }

      .form-label {
    font-weight: bold;
    margin-bottom: 5px;
    margin-top: 10px;
    display: block;
    color: #000 !important; /* Força a cor preta, ignorando qualquer herança */
}

        /* Cores customizadas dos botões */
        .btn-maiara { background-color: rgb(255, 242, 205); color: black; border: 1px solid #decba4; }
        .btn-maraisa { background-color: rgb(166, 202, 236); color: black; border: 1px solid #8ba8c4; }
        .btn-m2 { background-color: rgb(242, 242, 242); color: black; border: 1px solid #ccc; }
        .btn-fazenda { background-color: rgb(252, 114, 206); color: black; border: 1px solid #d15fa8; }
        
        .btn-report {
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
        }

        .card-header h4 { margin-bottom: 0; }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4>DETALHE DA ORDEM DE SERVIÇO
                            <button class="btn btn-danger float-end" onclick="window.history.back();">
                                <span class="bi-arrow-left-circle"></span>&nbsp;Voltar
                            </button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $os_codigo_id = mysqli_real_escape_string($conn, $_GET['id']);
                            $sql = "SELECT * FROM ordem_servico WHERE codigo='$os_codigo_id'";
                            $query = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($query) > 0) {
                                $os = mysqli_fetch_array($query);

                                // Formatação do código
                                $codigo_raw = str_pad($os['codigo'], 4, '0', STR_PAD_LEFT);
                                $cod_formatado = substr($codigo_raw, 0, 1) . '.' . substr($codigo_raw, 1);
                        ?>
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="form-label">CÓDIGO</label>
                                        <div class="view-field"><?= $cod_formatado ?></div>
                                    </div>
                                    <div class="col-md-10">
                                        <label class="form-label">NOME</label>
                                        <div class="view-field"><?= mb_strtoupper($os['nome']); ?></div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-3">
                                        <label class="form-label">CPF</label>
                                        <div class="view-field"><?= $os['cpf'] ?: '---'; ?></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">CNPJ</label>
                                        <div class="view-field"><?= $os['cnpj'] ?: '---'; ?></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">CELULAR</label>
                                        <div class="view-field"><?= $os['celular'] ?: '---'; ?></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">TELEFONE FIXO</label>
                                        <div class="view-field"><?= $os['telefone_fixo'] ?: '---'; ?></div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <label class="form-label">ENDEREÇO</label>
                                        <div class="view-field"><?= mb_strtoupper($os['endereco']); ?></div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label">CIDADE</label>
                                        <div class="view-field"><?= mb_strtoupper($os['cidade']); ?></div>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">CEP</label>
                                        <div class="view-field"><?= $os['cep']; ?></div>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">DATA</label>
                                        <div class="view-field"><?= date('d/m/Y', strtotime($os['data'])) ?></div>
                                    </div>
                                    <div class="col-md-3">
    <label class="form-label">VALOR</label>
    <div class="view-field">
        <?php 
            if (is_numeric($os['valor'])) {
                echo 'R$ ' . number_format($os['valor'], 2, ',', '.');
            } else {
                echo htmlspecialchars($os['valor']);
            }
        ?>
    </div>
</div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <label class="form-label">DESCRIÇÃO</label>
                                        <div class="view-field" style="height: auto; min-height: 100px; white-space: pre-wrap;"><?= $os['descricao']; ?></div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <label class="form-label">FORMA DE PAGAMENTO</label>
                                        <div class="view-field" style="height: auto; white-space: pre-wrap;"><?= $os['forma_pagamento']; ?></div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <form action="reportmaiara.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?= $os['codigo']; ?>">
                                            <button type="submit" class="btn btn-maiara btn-report"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Relatório Maiara</button>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <form action="reportmaraisa.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?= $os['codigo']; ?>">
                                            <button type="submit" class="btn btn-maraisa btn-report"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Relatório Maraisa</button>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <form action="reportm2.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?= $os['codigo']; ?>">
                                            <button type="submit" class="btn btn-m2 btn-report"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Relatório M2</button>
                                        </form>
                                    </div>
                                    <div class="col-md-3">
                                        <form action="reportfazenda.php" method="get" target="_blank">
                                            <input type="hidden" name="cod_rel" value="<?= $os['codigo']; ?>">
                                            <button type="submit" class="btn btn-fazenda btn-report"><span class="bi-file-earmark-pdf-fill"></span>&nbsp;Relatório Fazenda</button>
                                        </form>
                                    </div>
                                </div>
                        <?php
                            } else {
                                echo "<div class='alert alert-warning'>Ordem de Serviço não encontrada.</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>