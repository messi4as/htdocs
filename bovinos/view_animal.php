<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id'])) {
  $cod_animal = mysqli_real_escape_string($conn, $_GET['id']);
  $query = "SELECT b.*, m.brinco AS brinco_mae, m.cod_animal AS id_mae 
            FROM bovinos b
            LEFT JOIN nascimentos n ON b.cod_animal = n.cod_bezerro
            LEFT JOIN bovinos m ON n.cod_mae = m.cod_animal
            WHERE b.cod_animal = '$cod_animal'";
  $result = mysqli_query($conn, $query);

  if (mysqli_num_rows($result) == 1) {
    $animal = mysqli_fetch_array($result);
    $observacao = str_replace('<br>', "\n", $animal['observacao']);
    $observacao_exibir = nl2br(htmlspecialchars($observacao));

    // Buscar ocorrências relacionadas ao animal
    $query_ocorrencias = "SELECT * FROM ocorrencias WHERE cod_animal = '$cod_animal' ORDER BY id desc";
    $result_ocorrencias = mysqli_query($conn, $query_ocorrencias);
    $ocorrencias = mysqli_fetch_all($result_ocorrencias, MYSQLI_ASSOC);
  } else {
    $_SESSION['message'] = "Animal não encontrado!";
    header("Location: index.php");
    exit(0);
  }
} else {
  $_SESSION['message'] = "ID do animal não fornecido!";
  header("Location: index.php");
  exit(0);
}
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

  <title>VISUALIZAR ANIMAL</title>
  <style>
    #preview-container {
      width: 200px;
      height: 200px;
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
              <h4>VISUALIZAR ANIMAL
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
              </h4>
            </div>
            <div class="card-body">
              <form action="salvar_animal_editado.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="cod_animal" value="<?= $animal['cod_animal']; ?>">

                <div class="form-container">
                  <div>
                    <div id="preview-container">
                      <?php if ($animal['imagem']): ?>
                        <img id="img-preview" src="<?= $animal['imagem']; ?>" alt="Imagem do Animal" class="img-preview">
                        <input type="hidden" name="imagem_atual" value="<?= $animal['imagem']; ?>">
                      <?php else: ?>
                        <img id="img-preview" class="img-preview">
                      <?php endif; ?>
                    </div>
                  </div>



                  <div class="form-container">
                    <div class="form-group">
                      <label for="brinco" class="form-label">BRINCO:</label>
                      <input type="text" name="brinco" class="form-control" value="<?= $animal['brinco']; ?>" required>

                      <label for="brinco_mae" class="form-label">BRINCO DA MÃE:</label>
                      <?php if ($animal['id_mae']): ?>
                        <a href="view_animal.php?id=<?= urlencode($animal['id_mae']); ?>" class="form-control" style="text-decoration: none;"><?= htmlspecialchars($animal['brinco_mae']); ?></a>
                      <?php else: ?>
                        <input type="text" name="brinco_mae" class="form-control" value="Não informado" readonly>
                      <?php endif; ?>

                      <label for="sexo" class="form-label">SEXO:</label>
                      <select name="sexo" class="form-control" required>
                        <option value="MACHO" <?= $animal['sexo'] == 'MACHO' ? 'selected' : ''; ?>>MACHO</option>
                        <option value="FÊMEA" <?= $animal['sexo'] == 'FÊMEA' ? 'selected' : ''; ?>>FÊMEA</option>
                      </select>
                    </div>

                    <div class="form-container">
                      <div class="form-group">
                        <label for="raca" class="form-label">RAÇA:</label>
                        <input type="text" name="raca" class="form-control" value="<?= $animal['raca']; ?>" required style="width:300px;">

                        <label for="tipo" class="form-label">TIPO:</label>
                        <input type="text" name="tipo" class="form-control" value="<?= $animal['tipo']; ?>">

                        <label for="agrupamento" class="form-label">GRUPO:</label>
                        <input type="text" name="agrupamento" class="form-control" value="<?= $animal['agrupamento']; ?>">
                      </div>

                      <div class="form-container">
                        <div class="form-group">
                          <label for="status" class="form-label">STATUS:</label>
                          <input type="text" name="status" class="form-control" value="<?= $animal['status']; ?>" style="width:400px;">

                          <label for="local" class="form-label">LOCAL:</label>
                          <input type="text" name="local" class="form-control" value="<?= $animal['local']; ?>" required>

                          <label for="lote" class="form-label">LOTE:</label>
                          <input type="text" name="lote" class="form-control" value="<?= $animal['lote']; ?>">
                        </div>

                      </div>
                    </div>
                  </div>
                </div>

                <div class="form-container">
                  <div class="form-group">
                    <label for="imagem" class="form-label">IMAGEM:</label>
                    <input type="file" name="imagem" class="form-control" onchange="previewImage(event)" style="width:420px;">

                    <label for="estratificacao" class="form-label">ESTRATIFICAÇÃO:</label>
                    <select name="estratificacao" class="form-control" required>
                      <option value="Macho, 0 a 12 meses" <?= $animal['estratificacao'] == 'Macho, 0 a 12 meses' ? 'selected' : ''; ?>>Macho, 0 a 12 meses</option>
                      <option value="Fêmea, 0 a 12 meses" <?= $animal['estratificacao'] == 'Fêmea, 0 a 12 meses' ? 'selected' : ''; ?>>Fêmea, 0 a 12 meses</option>
                      <option value="Macho, 13 a 24 meses" <?= $animal['estratificacao'] == 'Macho, 13 a 24 meses' ? 'selected' : ''; ?>>Macho, 13 a 24 meses</option>
                      <option value="Fêmea, 13 a 24 meses" <?= $animal['estratificacao'] == 'Fêmea, 13 a 24 meses' ? 'selected' : ''; ?>>Fêmea, 13 a 24 meses</option>
                      <option value="Macho, 25 a 36 meses" <?= $animal['estratificacao'] == 'Macho, 25 a 36 meses' ? 'selected' : ''; ?>>Macho, 25 a 36 meses</option>
                      <option value="Fêmea, 25 a 36 meses" <?= $animal['estratificacao'] == 'Fêmea, 25 a 36 meses' ? 'selected' : ''; ?>>Fêmea, 25 a 36 meses</option>
                      <option value="Macho, Acima de 36 meses" <?= $animal['estratificacao'] == 'Macho, Acima de 36 meses' ? 'selected' : ''; ?>>Macho, Acima de 36 meses</option>
                      <option value="Fêmea, Acima de 36 meses" <?= $animal['estratificacao'] == 'Fêmea, Acima de 36 meses' ? 'selected' : ''; ?>>Fêmea, Acima de 36 meses</option>
                    </select>
                  </div>

                  <div class="form-group">
                    <label for="data_nascimento" class="form-label">DATA DE NASCIMENTO:</label>
                    <input type="date" name="data_nascimento" class="form-control" value="<?= $animal['data_nascimento']; ?>" required style="width:300px;">

                    <label for="pasto" class="form-label">PASTO:</label>
                    <input type="text" name="pasto" class="form-control" value="<?= $animal['pasto']; ?>">
                  </div>

                  <div class="form-group">
                    <label for="situacao_atual" class="form-label">SITUAÇÃO ATUAL:</label>
                    <input type="text" name="situacao_atual" class="form-control" value="<?= $animal['situacao_atual']; ?>" style="width:400px;">
                  </div>
                </div>

                <label for="observacao" class="form-label">OBSERVAÇÃO:</label>
                <textarea name="observacao" class="form-control" style="height:150px;"><?= htmlspecialchars($animal['observacao']); ?></textarea>

                <br>
                <div>
                  <button type="submit" class="btn btn-success"><span class="bi-save"></span>&nbsp;Salvar</button>
                  <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ocorrenciaModal">Registrar Ocorrência</button>
                </div>
                <br>

                <table class="table" id="ocorrenciasTable">
                  <thead>
                    <tr>
                      <th style="text-align: center; vertical-align: middle;">DATA</th>
                      <th style="text-align: center; vertical-align: middle;">LOCAL</th>
                      <th style="text-align: center; vertical-align: middle;">TIPO</th>
                      <th style="text-align: center; vertical-align: middle;">PESO</th>
                      <th style="text-align: center; vertical-align: middle;">DESCRIÇÃO</th>
                      <th style="text-align: center; vertical-align: middle;">AÇÕES</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (!empty($ocorrencias)): ?>
                      <?php foreach ($ocorrencias as $ocorrencia): ?>
                        <tr data-id="<?= $ocorrencia['id']; ?>">
                          <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($ocorrencia['data'])); ?></td>
                          <td style="text-align: center; vertical-align: middle;"><?= $ocorrencia['local']; ?></td>
                          <td style="text-align: center; vertical-align: middle;"><?= $ocorrencia['tipo']; ?></td>
                          <td style="text-align: center; vertical-align: middle;"><?= $ocorrencia['peso']; ?></td>
                          <td style="text-align: left; vertical-align: middle;"><?= $ocorrencia['descricao']; ?></td>
                          <td style="text-align: center; vertical-align: middle;">
                            <!-- <button type="button" class="btn btn-danger btn-sm delete-ocorrencia">Excluir</button> -->
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="6">Nenhuma ocorrência registrada.</td>
                      </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="ocorrenciaModal" tabindex="-1" aria-labelledby="ocorrenciaModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="ocorrenciaModalLabel">REGISTRAR OCORRÊNCIA</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="ocorrenciaForm">
              <input type="hidden" name="cod_animal" value="<?= $animal['cod_animal']; ?>">
              <div class="mb-3">
                <label for="data" class="form-label">DATA:</label>
                <input type="date" class="form-control" id="data" name="data" required>
              </div>
              <div class="mb-3">
                <label for="local" class="form-label">LOCAL:</label>
                <input type="text" class="form-control" id="local" name="local" required>
              </div>
              <div class="mb-3">
                <label for="tipo" class="form-label">TIPO:</label>
                <input type="text" class="form-control" id="tipo" name="tipo" required>
              </div>
              <div class="mb-3">
                <label for="peso" class="form-label">PESO:</label>
                <input type="text" class="form-control" id="peso" name="peso">
              </div>
              <div class="mb-3">
                <label for="descricao" class="form-label">DESCRIÇÃO:</label>
                <textarea class="form-control" id="descricao" name="descricao" required></textarea>
              </div>
              <button type="submit" class="btn btn-success">SALVAR</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script>
    document.getElementById('ocorrenciaForm').addEventListener('submit', function(event) {
      event.preventDefault();

      var formData = new FormData(this);

      fetch('salvar_ocorrencia.php', {
          method: 'POST',
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          console.log(data); // Adicione esta linha para depuração
          if (data.success) {
            // Atualizar a tabela de ocorrências
            var table = document.getElementById('ocorrenciasTable');
            var newRow = table.insertRow();
            newRow.innerHTML = `
          <td>${data.ocorrencia.data}</td>
          <td>${data.ocorrencia.local}</td>
          <td>${data.ocorrencia.tipo}</td>
          <td>${data.ocorrencia.peso}</td>
          <td>${data.ocorrencia.descricao}</td>
        `;
            // Fechar o modal
            var modalElement = document.getElementById('ocorrenciaModal');
            var modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
            modalElement.classList.remove('show');
            modalElement.style.display = 'none';
            document.body.classList.remove('modal-open');
            document.querySelector('.modal-backdrop').remove();
            // Adicionar um pequeno atraso antes de recarregar a página
            setTimeout(function() {
              location.reload();
            }, 500); // 500 milissegundos de atraso
          } else {
            alert('Erro ao salvar ocorrência');
          }
        })
        .catch(error => console.error('Erro:', error));
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('.delete-ocorrencia').forEach(button => {
        button.addEventListener('click', function() {
          var row = this.closest('tr');
          var id = row.getAttribute('data-id');

          if (confirm('Tem certeza que deseja excluir esta ocorrência?')) {
            fetch('excluir_ocorrencia.php', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'id=' + id
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  row.remove();
                } else {
                  alert('Erro ao excluir ocorrência');
                }
              })
              .catch(error => console.error('Erro:', error));
          }
        });
      });
    });
  </script>
</body>

</html>