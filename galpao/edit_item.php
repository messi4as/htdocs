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
    <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- jQuery deve ser carregado primeiro -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Plugins que dependem do jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <!-- Bootstrap JS -->
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>


    <style>
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

        input[type="text"],
        textare,
        select,
        input[type="file"] {
            width: 100%;
        }

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

        #preview {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: none;
        }

        #document-preview-container {
            display: flex;
            flex-direction: row;
            gap: 10px;
        }

        iframe {
            width: 100%;
            height: 200px;
            border: 1px solid #ddd;
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

        .documento-item {
            margin-bottom: 10px;
        }

        .documento-link {
            margin-right: 10px;
        }

        .documento-botao {
            width: 80px;
            height: 40px;
        }
    </style>

    <title>EDITAR ITEM</title>

</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <?php include('/xampp/htdocs/mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
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
                                    $descricao = str_replace('<br>', "\n", $item['descricao']);
                                    $foto = $item['foto'];



                                    if (isset($item['anexo_documento'])) {
                                        $anexos_documentos = json_decode($item['anexo_documento'], true);
                                    } else {
                                        $anexos_documentos = []; // Defina um array vazio ou um valor padrão
                                    }

                                    // Buscar Movimentações relacionadas ao Item
                                    $query_movimentacoes = "SELECT * FROM movimentacao WHERE id_item = '$id_item' ORDER BY id desc";
                                    $result_movimentacoes = mysqli_query($conn, $query_movimentacoes);
                                    $movimentacoes = mysqli_fetch_all($result_movimentacoes, MYSQLI_ASSOC);
                                } else {
                                    $_SESSION['message'] = "Movimenração não encontrado!";
                                    header("Location: index.php");
                                    exit(0);
                                }
                            } else {
                                $_SESSION['message'] = "ID do animal não fornecido!";
                                header("Location: index.php");
                                exit(0);
                            }


                            ?>

                            <script>
                                function excluirDocumentoItem(caminho) {
                                    if (confirm('Tem certeza que deseja excluir este documento?')) {
                                        var form = document.createElement('form');
                                        form.method = 'POST';
                                        form.action = 'excluir_documento_item.php';

                                        var inputCaminho = document.createElement('input');
                                        inputCaminho.type = 'hidden';
                                        inputCaminho.name = 'caminho_documento';
                                        inputCaminho.value = caminho;

                                        var inputIdItem = document.createElement('input');
                                        inputIdItem.type = 'hidden';
                                        inputIdItem.name = 'id_item';
                                        inputIdItem.value = '<?= $item['cod_item'] ?>';

                                        var inputDocumentosAtuais = document.createElement('input');
                                        inputDocumentosAtuais.type = 'hidden';
                                        inputDocumentosAtuais.name = 'documentos_atuais';
                                        inputDocumentosAtuais.value = '<?= json_encode($anexos_documentos) ?>';

                                        form.appendChild(inputCaminho);
                                        form.appendChild(inputIdItem);
                                        form.appendChild(inputDocumentosAtuais);
                                        document.body.appendChild(form);
                                        form.submit();
                                    }
                                }
                            </script>
                            <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit=" addCurrencyPrefix();">
                                <input type="hidden" name="id" value="<?= $item['cod_item'] ?>">
                                <input type="hidden" name="foto_atual" value="<?= $foto ?>">
                                <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($anexos_documentos) ?>'>

                                <div class="form-container">
                                    <div id="preview-container">
                                        <img id="preview" src="<?= $foto ?>" alt="Pré-visualização da Foto" style="display: block;">
                                    </div>

                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome_item" value="<?= $item['nome_item'] ?>" class="form-control" style="width:300px;" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;CATEGORIA:</label>
                                            <input type="text" name="categoria" value="<?= $item['categoria'] ?>" class="form-control" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;FOTO:</label>
                                            <input id="foto" type="file" name="foto" class="form-control" accept="image/*" onchange="previewImage(event)" style="width:300px;">
                                        </div>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">&nbsp;QUANTIDADE:</label>
                                                <input id="quantidade" type="text" name="quantidade" value="<?= $item['quantidade'] ?>" class="form-control" onchange="convertToUppercase()">

                                                <label class="form-label">&nbsp;VALOR:</label>
                                                <input id="valor" type="text" name="valor" value="<?= $item['valor'] ?>" class="form-control" oninput="formatarValor(this)">

                                                <label class="form-label">&nbsp;STATUS:</label>
                                                <input id="status" type="text" name="status" value="<?= $item['status'] ?>" class="form-control" onchange="convertToUppercase()">
                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label class="form-label">ORIGEM:</label>
                                                    <input id="origem" type="text" name="origem" value="<?= $item['origem'] ?>" class="form-control" style="width:400px;" onchange="convertToUppercase()">

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

                                                    <label for="data_entrada" class="form-label">DATA DE ENTRADA:</label>
                                                    <input type="date" name="data_entrada" value="<?= $item['data_entrada'] ?>" class="form-control" required>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label for="descricao" class="form-label">&nbsp;DESCRIÇÃO:</label>
                                <textarea name="descricao" class="form-control" style="height:150px;"><?= htmlspecialchars($item['descricao']); ?></textarea>


                                <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                <input id="documentos" type="file" name="anexo_documento[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <br>

                                <div id="document-preview-container">
                                    <div class="form-group">


                                        <?php
                                        // Supondo que $documentos contenha os caminhos dos documentos atuais
                                        if (!empty($anexos_documentos) && is_array($anexos_documentos)) {
                                            foreach ($anexos_documentos as $documento_atual) {
                                                echo '<div style="margin-bottom: 10px;">';
                                                echo '<a class="documento-link" href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                // echo ' <button class="btn btn-danger documento-botao" type="button" onclick="excluirDocumentoItem(\'' . $documento_atual . '\')">Excluir</button>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>


                                    </div>
                                </div>
                                <br>

                                <div>
                                    <button type="submit" name="edit_itens" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#movimentacaoModal" style="width:200px;height:50px;">Registrar Movimentação</button>
                                </div>

                                <br>

                                <table class="table" id="movimetacoesTable">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; vertical-align: middle;">DATA</th>
                                            <th style="text-align: center; vertical-align: middle;">DESCRIÇÃO</th>
                                            <th style="text-align: center; vertical-align: middle;">RESPONSÁVEL</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($movimentacoes)): ?>
                                            <?php foreach ($movimentacoes as $movimentacao): ?>
                                                <tr data-id="<?= $movimentacao['id']; ?>">
                                                    <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($movimentacao['data_movimentacao'])); ?></td>
                                                    <td style="text-align: left; vertical-align: middle;"><?= $movimentacao['descricao']; ?></td>
                                                    <td style="text-align: center; vertical-align: middle;"><?= $movimentacao['responsavel']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6">Nenhuma movimentação registrada.</td>
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
    </div>
    <script src="js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script>
        $(document).ready(function() {

            $('#renavan').mask('0000000000000');
            $('#placa').mask('AAA-9A99');
            $('#uf').mask('AA');

        });



        function formatarValor(input) {
            var valor = input.value.replace(/\D/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
            input.value = valor;
        }

        function addCurrencyPrefix() {
            var valorInput = document.getElementById('valor');
            if (valorInput.value && !valorInput.value.startsWith('R$')) {
                valorInput.value = 'R$ ' + valorInput.value;
            }
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('preview');
                output.src = reader.result;
                output.style.display = 'block';
            };
            reader.readAsDataURL(event.target.files[0]);
        }

        function previewDocuments(event) {
            var files = event.target.files;
            var container = document.getElementById('document-preview-container');
            container.innerHTML = '';
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                var url = URL.createObjectURL(file);
                var iframe = document.createElement('iframe');
                iframe.src = url;
                iframe.style.width = '300px';
                iframe.style.height = '400px';
                iframe.style.border = '1px solid #ddd';
                container.appendChild(iframe);
            }
        }
    </script>



    <!-- Modal -->
    <div class="modal fade" id="movimentacaoModal" tabindex="-1" aria-labelledby="movimentacaoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="movimentacaoModalLabel">REGISTRAR MOVIMENTAÇÃO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="movimentacaoForm">
                        <input type="hidden" name="id_item" value="<?= $item['cod_item']; ?>">
                        <div class="mb-3">
                            <label for="data_movimentacao" class="form-label">DATA:</label>
                            <input type="date" class="form-control" id="data" name="data_movimentacao" required>
                        </div>
                        <div class="mb-3">
                            <label for="descricao" class="form-label">DESCRIÇÃO:</label>
                            <textarea class="form-control" id="descricao" name="descricao" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="responsavel" class="form-label">RESPONSÁVEL:</label>
                            <input type="text" class="form-control" id="responsavel" name="responsavel">
                        </div>
                        <button type="submit" class="btn btn-success">SALVAR</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#movimentacaoForm').on('submit', function(event) {
                event.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: 'salvar_movimentacao.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            var data = JSON.parse(response);
                            console.log(data); // Adicione esta linha para depuração
                            if (data.success) {
                                // Atualizar a tabela de movimentações
                                var table = $('#movimentacoesTable');
                                var newRow = $('<tr>');
                                newRow.append('<td>' + data.movimentacao.data_movimentacao + '</td>');
                                newRow.append('<td>' + data.movimentacao.descricao + '</td>');
                                newRow.append('<td>' + data.movimentacao.responsavel + '</td>');
                                table.append(newRow);
                                // Fechar o modal
                                $('#movimentacaoModal').modal('hide');
                                // Adicionar um pequeno atraso antes de recarregar a página
                                setTimeout(function() {
                                    location.reload();
                                }, 500); // 500 milissegundos de atraso
                            } else {
                                alert('Erro ao salvar movimentação: ' + data.error);
                            }
                        } catch (e) {
                            console.error('Erro ao processar resposta:', e);
                            alert('Erro ao salvar movimentação');
                        }
                    },
                    error: function(error) {
                        console.error('Erro:', error);
                        alert('Erro ao salvar movimentação');
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-movimentacao').forEach(button => {
                button.addEventListener('click', function() {
                    var row = this.closest('tr');
                    var id = row.getAttribute('data-id');

                    if (confirm('Tem certeza que deseja excluir esta Movimentação?')) {
                        fetch('excluir_movimentacao.php', {
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
                                    alert('Erro ao excluir Movimentação');
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