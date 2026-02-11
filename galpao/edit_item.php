<?php
session_start();
require 'db_connect.php';

// Função para converter os <br> do banco em quebras de linha para o textarea
function br2nl($text) {
    return preg_replace('/<br\s*?\/?>/i', "\n", $text);
}
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link href="/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>
    
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>

    <style>
        .form-container { display: flex; flex-direction: row; gap: 20px; }
        .form-group { display: flex; flex-direction: column; margin-bottom: 10px; }
        .form-label { font-weight: bold; margin-bottom: 5px; }
        #preview-container { width: 200px; height: 200px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 10px; }
        #preview { width: 100%; height: 100%; object-fit: contain; }
        .galeria-item { position: relative; border: 1px solid #ddd; padding: 5px; border-radius: 5px; }
        .btn-excluir-foto { position: absolute; top: -5px; right: -5px; padding: 0px 5px; font-size: 12px; border-radius: 50%; }
        .table-container { width: 100%; overflow-x: auto; }
    </style>

    <title>EDITAR ITEM</title>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h4>EDITAR ITEM
                            <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                        </h4>
                    </div>

                    <div class="card-body">
                        <?php
                        if (isset($_GET['id'])) {
                            $id_item = mysqli_real_escape_string($conn, $_GET['id']);
                            $sql = "SELECT * FROM galpao WHERE cod_item='$id_item'";
                            $query = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($query) > 0) {
                                $item = mysqli_fetch_array($query);
                                $foto = $item['foto'];
                                $anexos_documentos = json_decode($item['anexo_documento'], true) ?? [];
                                
                                // Buscar Movimentações
                                $query_movimentacoes = "SELECT * FROM movimentacao WHERE id_item = '$id_item' ORDER BY id desc";
                                $result_movimentacoes = mysqli_query($conn, $query_movimentacoes);
                                $movimentacoes = mysqli_fetch_all($result_movimentacoes, MYSQLI_ASSOC);
                            }
                        }
                        ?>

                        <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="addCurrencyPrefix();">
                            <input type="hidden" name="id" value="<?= $item['cod_item'] ?>">
                            <input type="hidden" name="foto_atual" value="<?= $foto ?>">
                            <input type="hidden" name="documentos_atuais" value='<?= json_encode($anexos_documentos) ?>'>

                            <div class="form-container">
                                <div id="preview-container">
                                    <img id="preview" src="<?= $foto ?>" alt="Foto" style="display: <?= $foto ? 'block' : 'none' ?>;">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">NOME:</label>
                                    <input type="text" name="nome_item" value="<?= $item['nome_item'] ?>" class="form-control" style="width:300px;" onchange="convertToUppercase()">
                                    
                                    <label class="form-label">CATEGORIA:</label>
                                    <input type="text" name="categoria" value="<?= $item['categoria'] ?>" class="form-control" onchange="convertToUppercase()">
                                    
                                    <label class="form-label">FOTO PRINCIPAL:</label>
                                    <input id="foto" type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">QUANTIDADE:</label>
                                    <input type="text" name="quantidade" value="<?= $item['quantidade'] ?>" class="form-control">
                                    
                                    <label class="form-label">VALOR:</label>
                                    <input id="valor" type="text" name="valor" value="<?= $item['valor'] ?>" class="form-control" oninput="formatarValor(this)">
                                    
                                    <label class="form-label">STATUS:</label>
                                    <input type="text" name="status" value="<?= $item['status'] ?>" class="form-control" onchange="convertToUppercase()">
                                </div>

                                <div class="form-group">
                                    <label class="form-label">ORIGEM:</label>
                                    <input type="text" name="origem" value="<?= $item['origem'] ?>" class="form-control" style="width:350px;" onchange="convertToUppercase()">

                                    <label for="local" class="form-label">LOCAL:</label>
                                                    <select name="local" class="form-control" required>
                                                        <option value="TÉRREO - SALA 01" <?= $item['local'] == 'TÉRREO - SALA 01' ? 'selected' : ''; ?>>TÉRREO - SALA 01</option>
                                                        <option value="CAMARIM 01" <?= $item['local'] == 'CAMARIM 01' ? 'selected' : ''; ?>>CAMARIM 01</option>
                                                        <option value="CAMARIM 02" <?= $item['local'] == 'CAMARIM 02' ? 'selected' : ''; ?>>CAMARIM 02</option>
                                                        <option value="BOX 01" <?= $item['local'] == 'BOX 01' ? 'selected' : ''; ?>>BOX 01</option>
                                                        <option value="BOX 02" <?= $item['local'] == 'BOX 02' ? 'selected' : ''; ?>>BOX 02</option>
                                                        <option value="BOX 03" <?= $item['local'] == 'BOX 03' ? 'selected' : ''; ?>>BOX 03</option>
                                                        <option value="BOX 04" <?= $item['local'] == 'BOX 04' ? 'selected' : ''; ?>>BOX 04</option>
                                                        <option value="BOX 05" <?= $item['local'] == 'BOX 05' ? 'selected' : ''; ?>>BOX 05</option>
                                                        <option value="BOX 06" <?= $item['local'] == 'BOX 06' ? 'selected' : ''; ?>>BOX 06</option>
                                                        <option value="BOX 07" <?= $item['local'] == 'BOX 07' ? 'selected' : ''; ?>>BOX 07</option>
                                                        <option value="BOX 08" <?= $item['local'] == 'BOX 08' ? 'selected' : ''; ?>>BOX 08</option>
                                                        <option value="BOX 09" <?= $item['local'] == 'BOX 09' ? 'selected' : ''; ?>>BOX 09</option>
                                                        <option value="BOX 10" <?= $item['local'] == 'BOX 10' ? 'selected' : ''; ?>>BOX 10</option>
                                                        <option value="BOX 11" <?= $item['local'] == 'BOX 11' ? 'selected' : ''; ?>>BOX 11</option>
                                                        <option value="1º ANDAR - SALA 01" <?= $item['local'] == '1º ANDAR - SALA 01' ? 'selected' : ''; ?>>1º ANDAR - SALA 01</option>
                                                        <option value="1º ANDAR - SALA 02" <?= $item['local'] == '1º ANDAR - SALA 02' ? 'selected' : ''; ?>>1º ANDAR - SALA 02</option>
                                                        <option value="1º ANDAR - SALA 03" <?= $item['local'] == '1º ANDAR - SALA 03' ? 'selected' : ''; ?>>1º ANDAR - SALA 03</option>
                                                    </select>

                                    <label class="form-label">DATA DE ENTRADA:</label>
                                    <input type="date" name="data_entrada" value="<?= $item['data_entrada'] ?>" class="form-control">
                                </div>
                            </div>

                            <div class="mt-3">
                                <label class="form-label">DESCRIÇÃO:</label>
                                <textarea name="descricao" class="form-control" style="height:150px;"><?= br2nl($item['descricao']); ?></textarea>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <label class="form-label">GALERIA DE IMAGENS:</label>
                                    <input type="file" name="galeria[]" class="form-control mb-2" accept="image/*" multiple>
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php
                                        $q_f = mysqli_query($conn, "SELECT * FROM galpao_imagens WHERE id_item = '$id_item'");
                                        while($f = mysqli_fetch_assoc($q_f)): ?>
                                            <div class="galeria-item">
                                                <img src="<?= $f['caminho_imagem'] ?>" style="width: 70px; height: 70px; object-fit: cover;">
                                                <a href="excluir_foto_galeria.php?id=<?= $f['id'] ?>&item=<?= $id_item ?>" class="btn btn-danger btn-excluir-foto">×</a>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">DOCUMENTOS:</label>
                                    <input type="file" name="anexo_documento[]" class="form-control mb-2" multiple>
                                    <?php foreach ($anexos_documentos as $doc): ?>
                                        <div class="small"><i class="bi bi-file-earmark-text"></i> <?= basename($doc) ?></div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" name="edit_itens" class="btn btn-success" style="width:200px; height:50px;"><i class="bi bi-check-lg"></i> Salvar Alterações</button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#movimentacaoModal" style="width:250px; height:50px;"><i class="bi bi-plus-circle"></i> Registrar Movimentação</button>
                            </div>
                        </form>

                        <hr class="my-5">

                        <h5>HISTÓRICO DE MOVIMENTAÇÕES</h5>
                        <table class="table table-bordered table-striped" id="movimentacoesTable">
                            <thead>
                                <tr>
                                    <th style="width: 150px;">DATA</th>
                                    <th>DESCRIÇÃO</th>
                                    <th>RESPONSÁVEL</th>
                                    <th style="width: 50px;">AÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($movimentacoes)): ?>
                                    <?php foreach ($movimentacoes as $mov): ?>
                                        <tr data-id="<?= $mov['id']; ?>">
                                            <td><?= date('d/m/Y', strtotime($mov['data_movimentacao'])); ?></td>
                                            <td><?= $mov['descricao']; ?></td>
                                            <td><?= $mov['responsavel']; ?></td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-danger btn-sm delete-movimentacao"><i class="bi bi-trash"></i></button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center">Sem registros.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="movimentacaoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">REGISTRAR MOVIMENTAÇÃO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="movimentacaoForm">
                    <div class="modal-body">
                        <input type="hidden" name="id_item" value="<?= $item['cod_item']; ?>">
                        <div class="mb-3">
                            <label class="form-label">DATA:</label>
                            <input type="date" class="form-control" name="data_movimentacao" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">DESCRIÇÃO:</label>
                            <textarea class="form-control" name="descricao" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">RESPONSÁVEL:</label>
                            <input type="text" class="form-control" name="responsavel" onchange="this.value = this.value.toUpperCase()">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">SALVAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Salvar Movimentação (Ajax)
            $('#movimentacaoForm').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    url: 'salvar_movimentacao.php',
                    type: 'POST',
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        location.reload();
                    }
                });
            });

            // Excluir Movimentação
            $('.delete-movimentacao').on('click', function() {
                var row = $(this).closest('tr');
                var id = row.data('id');
                if (confirm('Excluir esta movimentação?')) {
                    $.post('excluir_movimentacao.php', { id: id }, function(data) {
                        row.remove();
                    });
                }
            });
        });

        function formatarValor(input) {
            var valor = input.value.replace(/\D/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
            input.value = valor;
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                document.getElementById('preview').src = reader.result;
                document.getElementById('preview').style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function convertToUppercase() {
            document.querySelectorAll('input[type="text"], textarea').forEach(i => i.value = i.value.toUpperCase());
        }
        
        function addCurrencyPrefix() {
            var v = document.getElementById('valor');
            if (v.value && !v.value.startsWith('R$')) v.value = 'R$ ' + v.value;
        }
    </script>
</body>
</html>