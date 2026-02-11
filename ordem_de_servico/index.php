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
    .table-container { width: 100%; overflow-x: auto; }
    
    table { width: 100%; border-collapse: collapse; background-color: #ffffff; }

    th, td { 
      border: 1px solid #dee2e6; 
      padding: 12px 10px; 
      vertical-align: middle; 
    }

    th { 
      background-color: #f8f9fa; 
      font-weight: 600;
      text-transform: uppercase;
      font-size: 0.85rem;
    }

    /* Controle da Descrição para não quebrar o layout */
    .col-descricao {
      min-width: 300px;
      max-width: 600px;
      font-size: 0.9rem;
      line-height: 1.4;
      text-align: justify;
      color: #333;
    }

    /* Destaque para o Valor */
    .col-valor {
      font-weight: bold;
      white-space: nowrap;
      color: #000;
    }
  </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>
        <div class="row">
                      <div class="col-md-12 mb-3">

                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
        <h4>HISTÓRICO DE ORDENS DE SERVIÇO
          <a href="formulario.php" class="btn btn-success float-end">
            <i class="bi bi-file-earmark-plus-fill"></i> Nova Ordem
          </a>
        </h4>
      </div>

      <div class="card-body">
        <form method="GET" action="" class="mb-3">
          <div class="input-group">
            <input type="text" name="nome" class="form-control" placeholder="Buscar por Nome">
            <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i> Buscar</button>
          </div>
        </form>

        <?php
        $nome = isset($_GET['nome']) ? mysqli_real_escape_string($conn, $_GET['nome']) : '';
        $sql = "SELECT * FROM ordem_servico";
        if ($nome != '') { $sql .= " WHERE nome LIKE '%$nome%'"; }
        $sql .= " ORDER BY codigo DESC";
        $os = mysqli_query($conn, $sql);
        $quantidade = mysqli_num_rows($os);
        ?>

        <div class="alert alert-info py-2">
          Ordens de Serviço: <strong><?= number_format($quantidade, 0, ',', '.'); ?></strong>
        </div>

        <div class="table-container">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th class="text-center col-valor">CÓDIGO</th>
                <th class="text-center col-valor">DATA</th>
                <th class="text-center col-valor">NOME</th>
                <th class="text-center col-valor">DESCRIÇÃO</th>
                <th class="text-center col-valor">VALOR</th>
                <th class="text-center col-valor">AÇÕES</th>
              </tr>
            </thead>

            <tbody>
              <?php if ($quantidade > 0): foreach ($os as $oss): 
                $codigo = str_pad($oss['codigo'], 4, '0', STR_PAD_LEFT);
                $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);
              ?>
                <tr>
                  <td class="text-center"><strong><?= $cod_formatado ?></strong></td>
                  <td class="text-center text-nowrap"><?= date('d/m/Y', strtotime($oss['data'])) ?></td>
                  <td style="font-weight: 500;"><?= htmlspecialchars($oss['nome']) ?></td>
                  <td class="col-descricao">
                    <?= $oss['descricao'] // Renderiza HTML do banco (<b>, <br>, etc) ?>
                  </td>
                  <td class="text-center col-valor">
  <?php 
    if (is_numeric($oss['valor'])) {
        // Se for número, imprime o R$ e formata com vírgula
        echo 'R$ ' . number_format($oss['valor'], 2, ',', '.');
    } else {
        // Se já tiver texto (como o R$ vindo do banco), imprime direto
        echo htmlspecialchars($oss['valor']);
    }
  ?>
</td>
                  <td class="text-center text-nowrap">
                    <a href="view_os.php?id=<?= $oss['codigo'] ?>" class="btn btn-secondary btn-sm" title="Visualizar"><i class="bi bi-eye-fill"></i></a>
                    <a href="edit_os.php?id=<?= $oss['codigo'] ?>" class="btn btn-success btn-sm" title="Editar"><i class="bi bi-pencil-fill"></i></a>
                  </td>
                </tr>
              <?php endforeach; else: ?>
                <tr><td colspan="6" class="text-center py-4">Nenhum registro encontrado.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</body>
</html>