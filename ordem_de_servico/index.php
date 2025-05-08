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
  <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <title>ORDEM DE SERVIÇO</title>

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
              <h4>HISTÓRICO DE ORDENS DE SERVIÇO
                <a href="formulario.php" class="btn btn-success float-end"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Nova Ordem de Serviço</a>
              </h4>
            </div>
            <div class="card-body">

              <form method="GET" action="">
                <div class="input-group mb-3">
                  <input type="text" name="nome" class="form-control" placeholder="Buscar por Nome">
                  <button class="btn btn-primary" type="submit"><span class="bi-search"></span>&nbsp;Buscar</button>
                </div>
              </form>

              <?php
              $nome = '';
              if (isset($_GET['nome'])) {
                $nome = mysqli_real_escape_string($conn, $_GET['nome']);
              }

              $sql = "SELECT * FROM ordem_servico";
              if ($nome != '') {
                $sql .= " WHERE nome LIKE '%$nome%'";
              }
              $sql .= " ORDER BY codigo DESC";
              $os = mysqli_query($conn, $sql);

              $quantidade = mysqli_num_rows($os);
              ?>

              <div class="alert alert-info" role="alert">
                Quantidade de Ordens de Serviço Cadastradas: <?php echo number_format($quantidade, 0, ',', '.'); ?>
              </div>

              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th style="text-align: center;">CÓDIGO</th>
                    <th style="text-align: center;">DATA</th>
                    <th style="text-align: center; width:550px;">NOME</th>
                    <th style="text-align: center;">VALOR</th>
                    <th style="text-align: center;">AÇÕES</th>
                  </tr>
                </thead>

                <tbody>
                  <?php
                  if (mysqli_num_rows($os) > 0) {
                    foreach ($os as $oss) {
                      // Formata o código do recibo
                      $codigo = str_pad($oss['codigo'], 4, '0', STR_PAD_LEFT);
                      $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);
                  ?>

                      <tr>
                        <td style="text-align: center; vertical-align: middle;"><?= $cod_formatado ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($oss['data'])) ?></td>
                        <td style="word-wrap: break-word; text-align: left; vertical-align: middle;"><?= ($oss['nome']) ?></td>
                        <td style="text-align: center; vertical-align: middle;"><?= ($oss['valor']) ?></td>
                        <td style="text-align: center; vertical-align: middle;">
                          <a href="view_os.php?id=<?= $oss['codigo'] ?>" class="btn btn-secondary btn-sm"><span class="bi-eye-fill"></span>&nbsp;Visualizar</a>
                          <a href="edit_os.php?id=<?= $oss['codigo'] ?>" class="btn btn-success btn-sm"><span class="bi-pencil-fill"></span>&nbsp;Editar</a>
                           <!-- <form action="cadastrar.php" method="POST" class="d-inline">
                            <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_os" value="<?= $oss['codigo'] ?>" class="btn btn-danger btn-sm"><span class="bi-trash3-fill"></span>&nbsp;Excluir</button>
                          </form>  -->
                        </td>
                      </tr>
                  <?php
                    }
                  } else {
                    echo '<h5>Nenhuma Ordem de Serviço Encontrada</h5>';
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