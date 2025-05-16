<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
  <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <title>GALPÃO - PLANTA</title>

  <style>
    .center-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .terreo {
      margin: 20px 0;
    }

    .svg-container {
      display: flex;
      /* Adicione o dois-pontos aqui */
      justify-content: center;
      align-items: center;
      width: 100%;
      overflow: hidden;
    }

    .svg-content {
      width: 300%;
      height: auto;
    }
    :root {
      --btn-color: rgb(175, 166, 118); /* Cor dourada em RGB */
    }

    .btn-custom {
      background-color: var(--btn-color);
      color: black;
      border: none;
      padding: 10px 20px;
      font-size: 16px;
      cursor: pointer;
    }

    .btn-custom:hover {
      background-color: rgb(211, 191, 81); /* Cor darkgoldenrod em RGB */
    }
  </style>
</head>

<body>
  <?php include('/xampp/htdocs/navbar.php'); ?>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>GALPÃO - PLANTA
          
            <a href="lista_item.php" class="btn btn-custom float-end me-2">
            <span class="bi bi-card-checklist"></span>&nbsp;Lista de Itens
            
            </a>


            </h4>
          </div>
          <div class="card-body center-content">
            <h1>PLANTA DO GALPÃO - 1º ANDAR</h1>
            <div class="terreo svg-container">
              <object data="/galpao/1andar.svg" type="image/svg+xml" class="svg-content"></object>
            </div>

            <h1>PLANTA DO GALPÃO - TÉRREO</h1>
            <div class="terreo svg-container">
              <object data="/galpao/terreo.svg" type="image/svg+xml" class="svg-content"></object>
            </div>
            <h1>DIVISÃO DOS BOXES E AMBIENTES</h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>