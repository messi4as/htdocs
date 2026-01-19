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

    <title>Ordem de Serviço</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>


</head>


<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>ORDEM DE SERVIÇO
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                            </h4>
                        </div>
                        <div class="card-body">
                            <form action="cadastrar.php" method="post" id="osForm" onsubmit="convertToUppercase(); addCurrencyPrefix();">
                                <style>
                                    .form-container {
                                        display: flex;
                                        flex-wrap: wrap;
                                        gap: 20px;
                                        justify-content: space-between;
                                        /* Adicionado */
                                    }

                                    .form-group {
                                        display: flex;
                                        align-items: center;
                                        flex: 1;
                                        /* Adicionado */
                                        margin: 0 10px;
                                        /* Adicionado */
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
                                        /* Adicionado */
                                    }

                                    .btn {
                                        flex: 0 0 auto;
                                        /* Adicionado */
                                        margin-left: 10px;
                                        /* Adicionado */
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
                                <div>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">NOME:</label>
                                            <input type="text" name="nome" required class="form-control">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">CPF:</label>
                                            <input id="cpf" type="text" name="cpf" class="form-control">

                                            <label class="form-label">&nbsp;CNPJ:</label>
                                            <input id="cnpj" type="text" name="cnpj" class="form-control">

                                            <label class="form-label">&nbsp;CELULAR:</label>
                                            <input id="celular" type="text" name="celular" class="form-control">

                                            <label class="form-label">&nbsp;TELEFONE_FIXO:</label>
                                            <input id="fixo" type="text" name="telefone_fixo" class="form-control">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">ENDEREÇO: </label>
                                            <input type="text" name="endereco" class="form-control">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-container">
                                        <div class="form-group">
                                            <label class="form-label">CIDADE:</label>
                                            <input type="text" name="cidade" class="form-control">

                                            <label class="form-label">&nbsp;CEP:</label>
                                            <input id="cep" type="text" name="cep" class="form-control">

                                            <label class="form-label">&nbsp;DATA:</label>
                                            <input type="date" name="data" required class="form-control">

                                            <label class="form-label">&nbsp;VALOR:</label>
                                            <input id="valor" type="text" name="valor" required class="form-control" oninput="formatarValor(this)">
                                        </div>
                                    </div>
                                    <br>
                                     <div>
                                            <label><strong>DESCRIÇÃO:</strong></label>
                                            <div id="editor_descricao" class="quill-editor-container"></div>
                                            <textarea name="descricao" id="descricao" style="display:none;"></textarea>
                                        </div>
                                        <br>

                                        <div>
                                            <label><strong>FORMA DE PAGAMENTO:</strong></label>
                                            <div id="editor_forma_pagamento" class="quill-editor-container"></div>
                                            <textarea name="forma_pagamento" id="forma_pagamento" style="display:none;"></textarea>
                                        </div>
                                        <p>
                                        <p>
                                            <button type="submit" name="create_os" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                                    </div>
                            </form>
                        </div>
                    </div>

                    <script src="js/jquery.mask.min.js"></script>
                    <script>
                        $(document).ready(function() {
                            $('#cpf').mask('000.000.000-00', {
                                reverse: true
                            });
                            $('#cnpj').mask('00.000.000/0000-00', {
                                reverse: true
                            });
                            $('#celular').mask('(00) 0 0000-0000');
                            $('#fixo').mask('(00) 0000-0000');
                            $('#cep').mask('00.000-000');

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

                        function convertToUppercase() {
                            var inputs = document.querySelectorAll('input[type="text"], textarea');
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
                    ['bold', 'italic', 'underline'],
                    // [{
                    //    'header': [1, 2, 3, false]
                    // }],
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
                    ['bold', 'italic', 'underline'],
                    // [{
                    //    'header': [1, 2, 3, false]
                    // }],
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