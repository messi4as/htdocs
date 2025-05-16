<?php
session_start();
require 'db_connect.php';

if (isset($_GET['id'])) {
    $contrato_id = mysqli_real_escape_string($conn, $_GET['id']);
    $sql = "SELECT * FROM contratos WHERE id_contrato='$contrato_id'";
    $query = mysqli_query($conn, $sql);

    if (mysqli_num_rows($query) > 0) {
        $contrato = mysqli_fetch_array($query);
        $descricao = $contrato['descricao'];


        if (isset($contrato['anexo'])) {
            $documentos = json_decode($contrato['anexo'], true);
        } else {
            $documentos = [];
        }

        $query_aditivos = "SELECT * FROM aditivo WHERE id_contrato = '$contrato_id' ORDER BY id desc";
        $result_aditivos = mysqli_query($conn, $query_aditivos);
        $aditivos = mysqli_fetch_all($result_aditivos, MYSQLI_ASSOC);
    } else {
        $_SESSION['mensagem'] = "Contrato não encontrado.";
        header("Location: lista_contratos.php");
        exit(0);
    }
} else {
    $_SESSION['mensagem'] = "ID do contrato não especificado.";
    header("Location: lista_contratos.php");
    exit(0);
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
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="/js/jquery-3.7.1.slim.min.js"></script>
    <script src="/js/jquery.mask.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.7/jquery.inputmask.min.js"></script>

    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script>
    <script type="text/javascript" src="/js/ckeditor.js"></script>
    <script type="text/javascript" src="/js/ckeditor_config.js"></script>

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
        textarea,
        select {
            text-transform: none;
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



        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
    </style>

    <title>EDITAR CONTRATOS</title>

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
                            <h4>EDITAR CONTRATOS
                                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="cadastrar.php" method="post" enctype="multipart/form-data" onsubmit="addCurrencyPrefix(); convertToUppercase(); ">

                                <input type="hidden" name="edit_contratos" value="true">
                                <input type="hidden" name="id" value="<?= $contrato['id_contrato'] ?>">
                                <input type="hidden" name="documentos_atuais" value='<?php echo json_encode($documentos) ?>'>

                                <div class="form-container">

                                    <div class="form-container">

                                        <div class="form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome" value="<?= $contrato['nome'] ?>" class="form-control" style="width:500px;" onchange="convertToUppercase()">

                                            <label class="form-label">&nbsp;TIPO DE CONTRATO:</label>
                                            <select name="tipo_contrato" class="form-control" required>
                                                <option value="ALUGUEL" <?= $contrato['tipo_contrato'] == 'ALUGUEL' ? 'selected' : ''; ?>>ALUGUEL</option>
                                                <option value="CONTA GARANTIDA" <?= $contrato['tipo_contrato'] == 'CONTA GARANTIDA' ? 'selected' : ''; ?>>CONTA GARANTIDA</option>
                                                <option value="CRÉDITO RURAL" <?= $contrato['tipo_contrato'] == 'CRÉDITO RURAL' ? 'selected' : ''; ?>>CRÉDITO RURAL</option>
                                                <option value="EMPRÉSTIMO PESSOAL" <?= $contrato['tipo_contrato'] == 'EMPRÉSTIMO PESSOAL' ? 'selected' : ''; ?>>EMPRÉSTIMO PESSOAL</option>
                                                <option value="PLANO DE SAÚDE" <?= $contrato['tipo_contrato'] == 'PLANO DE SAÚDE' ? 'selected' : ''; ?>>PLANO DE SAÚDE</option>
                                                <option value="PRESTAÇÃO DE SERVIÇO" <?= $contrato['tipo_contrato'] == 'PRESTAÇÃO DE SERVIÇO' ? 'selected' : ''; ?>>PRESTAÇÃO DE SERVIÇO</option>
                                                <option value="SEGURO AUTO" <?= $contrato['tipo_contrato'] == 'SEGURO AUTO' ? 'selected' : ''; ?>>SEGURO AUTO</option>
                                                <option value="SEGURO DE VIDA" <?= $contrato['tipo_contrato'] == 'SEGURO DE VIDA' ? 'selected' : ''; ?>>SEGURO DE VIDA</option>
                                                <option value="SEGURO EMPRESARIAL" <?= $contrato['tipo_contrato'] == 'SEGURO EMPRESARIAL' ? 'selected' : ''; ?>>SEGURO EMPRESARIAL</option>
                                                <option value="SEGURO RESIDENCIAL" <?= $contrato['tipo_contrato'] == 'SEGURO RESIDENCIAL' ? 'selected' : ''; ?>>SEGURO RESIDENCIAL</option>
                                            </select>
                                        </div>

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">DATA DE INÍCIO:</label>
                                                <input type="date" name="data_inicial" class="form-control" value="<?= $contrato['data_inicial'] ?>">

                                                <label class="form-label">DATA DE TÉRMINIO:</label>
                                                <input type="date" name="data_final" class="form-control" value="<?= $contrato['data_final'] ?>">
                                            </div>

                                            <div class="form-container">
                                                <div class="form-group">
                                                    <label class="form-label">STATUS:</label>
                                                    <select name="status" class="form-control" required>
                                                        <option value="ATIVO" <?= $contrato['status'] == 'ATIVO' ? 'selected' : ''; ?>>ATIVO</option>
                                                        <option value="CANCELADO" <?= $contrato['status'] == 'CANCELADO' ? 'selected' : ''; ?>>CANCELADO</option>
                                                        <option value="FINALIZADO" <?= $contrato['status'] == 'FINALIZADO' ? 'selected' : ''; ?>>FINALIZADO</option>

                                                    </select>

                                                    <label class="form-label">VALOR:</label>
                                                    <input id="valor" type="text" name="valor" class="form-control" value="<?= $contrato['valor'] ?>" onchange="formatarValor(this)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <label class="form-label">&nbsp;DESCRIÇÃO:</label>
                                <textarea name="descricao" class="form-control" style="height:150px;"><?= $contrato['descricao']; ?></textarea>

                                <label class="form-label">&nbsp;Ações com os Documentos:</label>
                                <div>
                                    <input type="radio" name="acao_documentos" value="manter" checked> Manter anexos existentes e adicionar novos<br>
                                    <input type="radio" name="acao_documentos" value="substituir"> Substituir anexos existentes pelos novos
                                </div>
                                <br>

                                <label class="form-label">&nbsp;DOCUMENTOS:</label>
                                <input id="anexo" type="file" name="anexo[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                                <br>

                                <div id="document-preview-container">
                                    <div class="form-group">
                                        <?php
                                        if (!empty($documentos) && is_array($documentos)) {
                                            foreach ($documentos as $documento_atual) {
                                                echo '<div style="margin-bottom: 10px;">';
                                                echo '<a class="documento-link" href="' . $documento_atual . '" target="_blank">' . basename($documento_atual) . '</a>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <br>

                                <div>
                                    <button type="submit" name="edit_contratos" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Salvar</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#aditivoModal" style="width:250px;height:50px;">Registrar Aditivo Contratual</button>
                                </div>

                                <br>

                                <table class="table" id="aditivoTable">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center; vertical-align: middle;">DATA</th>
                                            <th style="text-align: center; vertical-align: middle;">DESCRIÇÃO</th>
                                            <th style="text-align: center; vertical-align: middle;">RESPONSÁVEL</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($aditivos)): ?>
                                            <?php foreach ($aditivos as $aditivo): ?>
                                                <tr data-id="<?= $aditivo['id']; ?>">
                                                    <td style="text-align: center; vertical-align: middle;"><?= date('d/m/Y', strtotime($aditivo['data_aditivo'])); ?></td>
                                                    <td style="text-align: left; vertical-align: middle;"><?= $aditivo['descricao']; ?></td>
                                                    <td style="text-align: center; vertical-align: middle;"><?= $aditivo['responsavel']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6">Nenhuma Aditivo registrado.</td>
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

        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(function(input) {
                input.value = input.value.toUpperCase();
            });
        }

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
    <!--

    <script type="text/javascript">
        bkLib.onDomLoaded(function() {
            nicEditors.allTextAreas()
        }); // convert all text areas to rich text editor on that page
        bkLib.onDomLoaded(function() {
            new nicEditor().panelInstance('area1');
        }); // convert text area with id area1 to rich text editor.
        bkLib.onDomLoaded(function() {
            new nicEditor({
                fullPanel: true
            }).panelInstance('area2');
        }); // convert text area with id area2 to rich text editor with full panel.
    </script>
    -->

    <!-- Modal -->
    <div class="modal fade" id="aditivoModal" tabindex="-1" aria-labelledby="aditivoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aditivoModalLabel">REGISTRAR ADITIVO DE CONTRATO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="aditivoForm">
                        <input type="hidden" name="id_contrato" value="<?= $contrato['id_contrato']; ?>">
                        <div class="mb-3">
                            <label for="data_aditivo" class="form-label">DATA:</label>
                            <input type="date" class="form-control" id="data" name="data_aditivo" required>
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

                    <script>
                        $(document).ready(function() {
                            $('#aditivoForm').on('submit', function(event) {
                                event.preventDefault();

                                var formData = new FormData(this);

                                $.ajax({
                                    url: 'salvar_aditivo.php',
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        try {
                                            var data = JSON.parse(response);
                                            console.log(data); // Adicione esta linha para depuração
                                            if (data.success) {
                                                // Atualizar a tabela de aditivos
                                                var table = $('#aditivoTable');
                                                var newRow = $('<tr>');
                                                newRow.append('<td>' + data.aditivo.data_aditivo + '</td>');
                                                newRow.append('<td>' + data.aditivo.descricao + '</td>');
                                                newRow.append('<td>' + data.aditivo.responsavel + '</td>');
                                                table.append(newRow);
                                                // Fechar o modal
                                                $('#aditivoModal').modal('hide');
                                                // Adicionar um pequeno atraso antes de recarregar a página
                                                setTimeout(function() {
                                                    location.reload();
                                                }, 500); // 500 milissegundos de atraso
                                            } else {
                                                alert('Erro ao salvar Aditivo: ' + data.error);
                                            }
                                        } catch (e) {
                                            console.error('Erro ao processar resposta:', e);
                                            alert('Erro ao salvar Aditivo');
                                        }
                                    },
                                    error: function(error) {
                                        console.error('Erro:', error);
                                        alert('Erro ao salvar Aditivo');
                                    }
                                });
                            });
                        });
                    </script>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelectorAll('.delete-aditivo').forEach(button => {
                                button.addEventListener('click', function() {
                                    var row = this.closest('tr');
                                    var id = row.getAttribute('data-id');

                                    if (confirm('Tem certeza que deseja excluir este Aditivo?')) {
                                        fetch('excluir_aditivo.php', {
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
                                                    alert('Erro ao excluir Aditivo');
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