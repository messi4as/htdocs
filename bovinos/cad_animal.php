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
  <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
  <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <title>NOVO ANIMAL</title>
  <style>
    #preview-container {
      width: 270px;
      height: 270px;
      border: 1px solid #ddd;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      margin-bottom: 10px;
    }

    .img-preview {
      width: 100%;
      height: 100%;

    }

    .form-container {
      display: flex;
      flex-direction: row;
      gap: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 10px;
    }

    .form-label {
      font-weight: bold;
      margin-bottom: 5px;
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
  <script>
    function previewImage(event) {
      var reader = new FileReader();
      reader.onload = function() {
        var output = document.getElementById('img-preview');
        output.src = reader.result;
      }
      reader.readAsDataURL(event.target.files[0]);
    }
  </script>
</head>

<body>
  <?php include('navbar.php'); ?>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="table-container">
            <div class="card-header">
              <h4>CADASTRO DE NOVO ANIMAL
                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</a>
              </h4>
            </div>
            <div class="card-body">
              <form action="salvar_animal.php" method="POST" enctype="multipart/form-data" onsubmit="convertToUppercase();">
                <div class="form-container">

                  <div id="preview-container">
                    <img id="img-preview" class="img-preview">
                  </div>

                  <div class="form-container">
                    <div class="form-group">
                      <label for="brinco" class="form-label">BRINCO:</label>
                      <input type="text" name="brinco" class="form-control" required>

                      <label for="sexo" class="form-label">SEXO:</label>
                      <select name="sexo" class="form-control" required>
                        <option value="" disabled selected>Selecione o Sexo</option>
                        <option value="MACHO">MACHO</option>
                        <option value="FÊMEA">FÊMEA</option>
                      </select> 

                      <label for="data_nascimento" class="form-label">DATA DE NASCIMENTO:</label>
                      <input type="date" name="data_nascimento" class="form-control" required>

                      <label for="imagem" class="form-label">FOTO:</label>
                      <input type="file" name="imagem" class="form-control" onchange="previewImage(event)">
                    </div>

                    <div class="form-container">
                      <div class="form-group">
                        <label for="raca" class="form-label">RAÇA:</label>
                        <select name="raca" class="form-control" required>
                          <option value="" disabled selected>Selecione a Raça</option>
                          <option value="NELORE">NELORE</option>
                          <option value="CRUZADO">CRUZADO</option>
                        </select>

                        <label for="tipo" class="form-label">TIPO:</label>
                        <select name="tipo" class="form-control" required>
                          <option value="" disabled selected>Selecione a Tipo</option>
                          <option value="BOVINO">BOVINO</option>
                        </select>

                        <label for="agrupamento" class="form-label">GRUPO:</label>
                        <select name="agrupamento" class="form-control" required>
                          <option value="" disabled selected>Selecione o Grupo</option>
                          <option value="BEZERRA">BEZERRA</option>
                          <option value="BEZERRA DE LEITE">BEZERRA DE LEITE</option>
                          <option value="BEZERRO">BEZERRO</option>
                          <option value="BEZERRO DE LEITE">BEZERRO DE LEITE</option>
                          <option value="GARROTE">GARROTE</option>
                          <option value="GARROTE DE LEITE">GARROTE DE LEITE</option>
                          <option value="NOVILHA">NOVILHA</option>
                          <option value="NOVILHA DE LEITE">NOVILHA DE LEITE</option>
                          <option value="TOURO">TOURO</option>
                          <option value="VACA">VACA</option>
                        </select>

                        <label for="situacao_atual" class="form-label">SITUAÇÃO ATUAL:</label>
                        <input type="text" name="situacao_atual" class="form-control">
                      </div>

                      <div class="form-container">
                        <div class="form-group">

                          <label for="status" class="form-label">STATUS:</label>
                          <select name="status" class="form-control" required style="width:400px;">
                            <option value="" disabled selected>Selecione o Status</option>
                            <option value="ATIVO">ATIVO</option>
                            <option value="MORTE">MORTE</option>
                            <option value="VENDIDO">VENDIDO</option>
                            <option value="DOADO">DOADO</option>
                            <option value="DESAPARECIDO">DESAPARECIDO</option>
                          </select>

                          <label for="local" class="form-label">LOCAL:</label>
                          <select name="local" class="form-control" required>
                            <option value="" disabled selected>Selecione o Local</option>
                            <option value="FAZENDA ROSADA">FAZENDA ROSADA</option>
                            <option value="FAZENDA MATO DO FURADO">FAZENDA MATO DO FURADO</option>
                          </select>

                          <label for="lote" class="form-label">LOTE:</label>
                          <input type="text" name="lote" class="form-control">

                          <label for="pasto" class="form-label">PASTO:</label>
                          <input type="text" name="pasto" class="form-control">
                        </div>

                      </div>
                    </div>
                  </div>
                </div>

                <label for="observacao" class="form-label">OBSERVAÇÃO:</label>
                <textarea name="observacao" class="form-control" style="height:150px;"></textarea>

                <br>
                <div>
                  <button type="submit" class="btn btn-success"><span class="bi-save"></span>&nbsp;Salvar</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
  </div>
</body>

</html>