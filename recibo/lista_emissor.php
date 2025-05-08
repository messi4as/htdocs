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
  <link href="css/docs.css" rel="stylesheet">
  <title>EMISSOR</title>
  <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
  <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


  <style>
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
  <?php include('navbar.php'); ?>
  <div class="container mt-4">
    <?php include('mensagem.php'); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="table-container">
            <div class="card-header">
              <h4>SÓCIOS / REPRESENTANTES CADASTRADOS
                <a href="cadastro_emissor.php" class="btn btn-success float-end"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</a>
              </h4>
            </div>
            <div class="card-body">
              <form method="GET" action="index.php">
                <div class="input-group mb-3">
                  <input type="text" name="nome_emitente" class="form-control" placeholder="Buscar por Nome">
                  <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                </div>
              </form>
              <?php
              $nome = '';
              if (isset($_GET['nome_emissor'])) {
                $nome = mysqli_real_escape_string($conn, $_GET['nome_emissor']);
              }

              $sql = "SELECT * FROM emissor";
              if ($nome != '') {
                $sql .= " WHERE nome_emissor LIKE '%$nome%'";
              }
              $sql .= " ORDER BY cod_emissor";
              $os = mysqli_query($conn, $sql);
              $quantidade = mysqli_num_rows($os);

              ?>

              <div class="alert alert-info" role="alert">
                Quantidade de Representantes Cadastrados: <?php echo number_format($quantidade, 0, ',', '.'); ?>
              </div>


              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th style="text-align: center; width:50px;">CÓDIGO</th>
                    <th style="text-align: center; width:300px;">NOME</th>
                    <th style="text-align: center; width:100px;">CPF</th>
                    <th style="text-align: center; width:100px;">CNPJ</th>
                    <th style="text-align: center; width:250px;">AÇÕES</th>
                  </tr>
                </thead>
                <tbody>

                  <?php

                  if (mysqli_num_rows($os) > 0) {
                    foreach ($os as $oss) {
                      // Formata o código do recibo
                      $cod_emissor = str_pad($oss['cod_emissor'], 4, '0', STR_PAD_LEFT);
                      $cod_emissor_formatado = substr($cod_emissor, 0, 1) . '.' . substr($cod_emissor, 1);
                  ?>
                      <tr>
                        <td style="text-align: center; vertical-align: middle;"><?= $cod_emissor_formatado ?></td>
                        <td style="text-align: left; vertical-align: middle;"><?= mb_strtoupper($oss['nome_emissor']) ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= $oss['cpf_emissor'] ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= $oss['cnpj_emissor'] ?></td>
                        <td style="text-align: center; vertical-align: middle;">
                          <a href="edit_emissor.php?id=<?= $oss['cod_emissor'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>


                        </td>
                      </tr>
                  <?php
                    }
                  } else {
                    echo '<h5>Nenhum Emitente Encontrado</h5>';
                  }
                  ?>
                </tbody>
              </table>

              

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>