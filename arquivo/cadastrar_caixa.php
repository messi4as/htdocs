<?php include('/xampp/htdocs/navbar.php'); ?> <!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <title>Cadastrar Caixa - Arquivo Morto</title>
  <style>
    :root { --btn-color: rgb(175, 166, 118); } /* Mantendo sua identidade visual */
    .btn-custom { background-color: var(--btn-color); color: black; border: none; }
    .btn-custom:hover { background-color: rgb(211, 191, 81); }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="card">
      <div class="card-header">
        <h4><i class="bi bi-box-seam"></i> Nova Caixa de Arquivo</h4>
      </div>
      <div class="card-body">
        <form action="salvar_caixa.php" method="POST">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Nome/Identificação da Caixa</label>
              <input type="text" name="nome_caixa" class="form-control" placeholder="Ex: Notas Fiscais 2023" required>
            </div>
            <div class="col-md-2 mb-3">
              <label>Armário</label>
              <select name="armario" class="form-select">
                <?php for($i=1; $i<=4; $i++) echo "<option value='$i'>$i</option>"; ?>
              </select>
            </div>
            <div class="col-md-2 mb-3">
              <label>Bandeja</label>
              <select name="bandeja" class="form-select">
                <?php foreach(range('A', 'F') as $letra) echo "<option value='$letra'>$letra</option>"; ?>
              </select>
            </div>
            <div class="col-md-2 mb-3">
              <label>Posição (1-6)</label>
              <input type="number" name="posicao" class="form-control" min="1" max="6" required>
            </div>
          </div>
          <button type="submit" class="btn btn-custom mt-3">Salvar Localização da Caixa</button>
        </form>
      </div>
    </div>
  </div>
</body>
</html>