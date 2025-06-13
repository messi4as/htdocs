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

    <title>Editar Ordem de Serviço</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <style>
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: space-between;
        }

        .form-group {
            display: flex;
            align-items: center;
            flex: 1;
            margin: 0 10px;
        }

        .form-label {
            font-weight: bold;
            margin-right: 10px;
        }

        input[type="text"],
        textarea,
        select {
            text-transform: none;
            width: 100%;
        }

        .btn {
            flex: 0 0 auto;
            margin-left: 10px;
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

        /* Estilo para o container do Quill, para que tenha a mesma altura e borda do textarea */
        .quill-editor-container {
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            min-height: 150px;
            /* Altura mínima para o editor */
            /* Remova padding-bottom e padding-top se o Quill.snow.css já estiver aplicando */
            padding-bottom: 42px;
            /* Espaço para a barra de rolagem do editor */
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>EDITAR ORDEM DE SERVIÇO
                                <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left-square-fill"></span>&nbsp;Voltar</a>
                            </h4>
                        </div>
                        <div class="card-body">
                            <?php
                            if (isset($_GET['id'])) {
                                $os_codigo = mysqli_real_escape_string($conn, $_GET['id']);
                                $sql = "SELECT * FROM ordem_servico WHERE codigo='$os_codigo'";
                                $query = mysqli_query($conn, $sql);

                                if (mysqli_num_rows($query) > 0) {
                                    $os_data = mysqli_fetch_array($query); // Renomeei para evitar conflito com $os_codigo como string
                                    $descricao = htmlspecialchars($os_data['descricao']); // Usar htmlspecialchars diretamente aqui
                                    $forma_pagamento = htmlspecialchars($os_data['forma_pagamento']);


                                    // Formata o código do recibo
                                    $codigo = str_pad($os_data['codigo'], 4, '0', STR_PAD_LEFT);
                                    $cod_formatado = substr($codigo, 0, 1) . '.' . substr($codigo, 1);

                            ?>
                                    <form action="cadastrar.php" method="post" id="osForm"> <input type="hidden" name="os_id" value="<?= $os_data['codigo'] ?>">

                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CÓDIGO</label>
                                                <a class="form-control" style="width:75px ; background-color: #e9ecef;"><?= $cod_formatado ?></a>
                                                <label class="form-label">&nbsp;NOME</label>
                                                <input type="text" name="nome" value="<?= htmlspecialchars($os_data['nome']) ?>" required class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CPF:</label>
                                                <input id="cpf" type="text" name="cpf" value="<?= htmlspecialchars($os_data['cpf']) ?>" class="form-control">

                                                <label class="form-label">&nbsp;CNPJ:</label>
                                                <input id="cnpj" type="text" name="cnpj" value="<?= htmlspecialchars($os_data['cnpj']) ?>" class="form-control">

                                                <label class="form-label">&nbsp;CELULAR:</label>
                                                <input id="celular" type="text" name="celular" value="<?= htmlspecialchars($os_data['celular']) ?>" class="form-control">

                                                <label class="form-label">&nbsp;TELEFONE_FIXO:</label>
                                                <input id="fixo" type="text" name="telefone_fixo" value="<?= htmlspecialchars($os_data['telefone_fixo']) ?>" class="form-control">
                                            </div>
                                        </div>
                                        <br>
                                        <div>
                                            <label><strong>ENDEREÇO: </strong></label>
                                            <input type="text" name="endereco" value="<?= htmlspecialchars($os_data['endereco']) ?>" class="form-control">
                                        </div>
                                        <br>
                                        <div class="form-container">
                                            <div class="form-group">
                                                <label class="form-label">CIDADE:</label>
                                                <input type="text" name="cidade" value="<?= htmlspecialchars($os_data['cidade']) ?>" class="form-control">

                                                <label class="form-label">&nbsp;CEP:</label>
                                                <input id="cep" type="text" name="cep" value="<?= htmlspecialchars($os_data['cep']) ?>" class="form-control">

                                                <label class="form-label">&nbsp;DATA:</label>
                                                <input type="date" name="data" required value="<?= htmlspecialchars($os_data['data']) ?>" class="form-control">

                                                <label class="form-label">&nbsp;VALOR:</label>
                                                <input id="valor" type="text" name="valor" required value="<?= htmlspecialchars($os_data['valor']) ?>" class="form-control" oninput="formatarValor(this), addCurrencyPrefix(this)">
                                            </div>
                                        </div>
                                        <br>

                                        <div>
                                            <label><strong>DESCRIÇÃO:</strong></label>
                                            <div id="editor_descricao" class="quill-editor-container"></div>
                                            <textarea name="descricao" id="descricao" style="display:none;"><?= $descricao ?></textarea>
                                        </div>
                                        <br>

                                        <div>
                                            <label><strong>FORMA DE PAGAMENTO:</strong></label>
                                            <div id="editor_forma_pagamento" class="quill-editor-container"></div>
                                            <textarea name="forma_pagamento" id="forma_pagamento" style="display:none;"><?= $forma_pagamento ?></textarea>
                                        </div>
                                        <p>

                                        <p>
                                            <button type="submit" name="edit_os" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-pencil-fill"></span>&nbsp;Salvar</button>
                                            <button type="submit" name="create_os" class="btn btn-primary" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Duplicar</button>
                                        </p>
                                    </form>
                            <?php
                                } else {
                                    echo "<h5>Ordem de Serviço não encontrada</h5>";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Scripts de máscaras (jQuery.mask)
        $('#cpf').mask('000.000.000-00', {
            reverse: true
        });
        $('#cnpj').mask('00.000.000/0000-00', {
            reverse: true
        });
        $('#celular').mask('(00) 0 0000-0000');
        $('#fixo').mask('(00) 0000-0000');
        $('#cep').mask('00.000-000');

        // Função de formatação de valor
        function formatarValor(input) {
            var valor = input.value.replace(/\D/g, '');
            valor = (valor / 100).toFixed(2) + '';
            valor = valor.replace(".", ",");
            valor = valor.replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
            input.value = valor;
        }

        // Função para adicionar prefixo R$
        function addCurrencyPrefix() {
            var valorInput = document.getElementById('valor');
            if (valorInput.value && !valorInput.value.startsWith('R$')) {
                valorInput.value = 'R$ ' + valorInput.value;
            }
        }
          </script>

          <script>
        // Esta função `convertToUppercase` só é relevante para inputs normais,
        // pois o Quill gerencia o conteúdo do editor.
        // Se você quer que o conteúdo do Quill seja salvo em maiúsculas,
        // faça o `toUpperCase()` na linha `quill.root.innerHTML` antes de atribuir ao textarea.
        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"]');
            inputs.forEach(function(input) {
                input.value = input.value.toUpperCase();
            });
        }

        // --- Configuração e Inicialização do Quill.js ---

        // Inicializa o editor para a descrição
        var quillDescricao = new Quill('#editor_descricao', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'script': 'sub'
                    }, {
                        'script': 'super'
                    }],
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            }
        });

        // Inicializa o editor para a forma de pagamento
        var quillFormaPagamento = new Quill('#editor_forma_pagamento', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    [{
                        'header': [1, 2, 3, false]
                    }],
                    [{
                        'list': 'ordered'
                    }, {
                        'list': 'bullet'
                    }],
                    [{
                        'script': 'sub'
                    }, {
                        'script': 'super'
                    }],
                    [{
                        'indent': '-1'
                    }, {
                        'indent': '+1'
                    }],
                    [{
                        'color': []
                    }, {
                        'background': []
                    }],
                    [{
                        'align': []
                    }],
                    ['clean']
                ]
            }
        });

        // --- Carregar o conteúdo existente do textarea para o editor Quill ---
        // Garante que o DOM esteja totalmente carregado antes de tentar carregar o conteúdo
        document.addEventListener('DOMContentLoaded', function() {
            var descricaoTextarea = document.getElementById('descricao');
            var formaPagamentoTextarea = document.getElementById('forma_pagamento');

            if (descricaoTextarea && descricaoTextarea.value) {
                // Use `dangerouslyPasteHTML` para carregar HTML no Quill
                quillDescricao.clipboard.dangerouslyPasteHTML(descricaoTextarea.value);
            }
            if (formaPagamentoTextarea && formaPagamentoTextarea.value) {
                quillFormaPagamento.clipboard.dangerouslyPasteHTML(formaPagamentoTextarea.value);
            }
        });

        // --- Atualizar o textarea oculto com o conteúdo do Quill antes do envio do formulário ---
        var meuFormulario = document.getElementById('osForm'); // Use o ID do seu formulário

        if (meuFormulario) {
            meuFormulario.addEventListener('submit', function() {
                // Pega o HTML do editor de descrição e coloca no textarea oculto
                // Você pode adicionar .toUpperCase() aqui se quiser que o HTML seja salvo em maiúsculas
                document.getElementById('descricao').value = quillDescricao.root.innerHTML;

                // Pega o HTML do editor de forma de pagamento e coloca no textarea oculto
                // Você pode adicionar .toUpperCase() aqui se quiser que o HTML seja salvo em maiúsculas
                document.getElementById('forma_pagamento').value = quillFormaPagamento.root.innerHTML;
            });
        } else {
            console.warn("Formulário com ID 'osForm' não encontrado. Certifique-se de que o Quill está sendo atualizado antes do envio.");
        }
    </script>
</body>

</html>