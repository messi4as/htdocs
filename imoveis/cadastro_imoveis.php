<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <title>CADASTRO DE IMÓVEIS</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

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
    </style>
</head>

<body>
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>CADASTRO DE IMÓVEIS
                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>
                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="cadastrar.php" method="post" id="imForm" enctype="multipart/form-data">


                            <div class="form-container">
                                <div class="form-group">
                                    <label class="form-label">NOME:</label>
                                    <input type="text" name="nome_imovel" class="form-control" onchange="convertToUppercase()" style="width:500px;">

                                    <label class="form-label">&nbsp;ENDEREÇO:</label>
                                    <input type="text" name="endereco_imovel" class="form-control" onchange="convertToUppercase()">

                                    <label class="form-label">&nbsp;BAIRRO:</label>
                                    <input type="text" name="bairro_imovel" class="form-control" onchange="convertToUppercase()">


                                </div>

                                <div class="form-container">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;CEP:</label>
                                        <input id="cep" type="text" name="cep_imovel" class="form-control" onchange="convertToUppercase()">

                                        <label class="form-label">&nbsp;LOCALIZAÇÃO:</label>
                                        <input type="text" name="localizacao_imovel" class="form-control" style="width:500px;">
                                    </div>



                                </div>


                            </div>

                            <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;PROPRIETÁRIO:</label>
                                                    <div id="editor_proprietario" class="quill-editor-container"></div>
                                                    <textarea name="proprietario_imovel" class="form-control" style="display:none;" id="proprietario_imovel"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;ENERGIA:</label>
                                                    <div id="editor_energia" class="quill-editor-container"></div>
                                                    <textarea name="energia_imovel" class="form-control" style="display:none;" id="energia_imovel"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;CONDOMÍNIO:</label>
                                                    <div id="editor_condominio" class="quill-editor-container"></div>
                                                    <textarea name="condominio_imovel" class="form-control" style="display:none;" id="condominio_imovel"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;GÁS:</label>
                                                    <div id="editor_gas" class="quill-editor-container"></div>
                                                    <textarea name="gas_imovel" class="form-control" style="display:none;" id="gas_imovel"></textarea>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;INSCRIÇÃO:</label>
                                                    <div id="editor_inscricao" class="quill-editor-container"></div>
                                                    <textarea name="inscricao_imovel" class="form-control" style="display:none;" id="inscricao_imovel"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;ÁGUA:</label>
                                                    <div id="editor_agua" class="quill-editor-container"></div>
                                                    <textarea name="agua_imovel" class="form-control" style="display:none;" id="agua_imovel"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;TV POR ASSINATURA:</label>
                                                    <div id="editor_tv" class="quill-editor-container"></div>
                                                    <textarea name="tv_imovel" class="form-control" style="display:none;" id="tv_imovel"></textarea>
                                                </div>

                                                <div class="form-group">
                                                    <label class="form-label">&nbsp;INTERNET:</label>
                                                    <div id="editor_internet" class="quill-editor-container"></div>
                                                    <textarea name="internet_imovel" class="form-control" style="display:none;" id="internet_imovel"></textarea>
                                                </div>
                                            </div>
                                        </div>
                            <label class="form-label">&nbsp;DOCUMENTOS:</label>
                            <input id="documentos" type="file" name="documentos_imoveis[]" class="form-control" accept=".pdf,.doc,.docx" multiple onchange="previewDocuments(event)">
                            <br>
                            <div id="document-preview-container"></div>
                            <br>
                            <div>
                                <button type="submit" name="cad_imoveis" class="btn btn-success" style="width:200px;height:50px;"><span class="bi-file-earmark-plus-fill"></span>&nbsp;Cadastrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="js/jquery.mask.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#renavan').mask('0000000000000');
            $('#placa').mask('AAA-9A99');
            $('#uf').mask('AA');
            $('#cep').mask('00.000-000');

        });

        function convertToUppercase() {
            var inputs = document.querySelectorAll('input[type="text"], textarea');
            inputs.forEach(function(input) {
                input.value = input.value.toUpperCase();
            });
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
      // --- Configuração e Inicialização do Quill.js ---

        // Inicializa o editor para a proprietario
        var quillProprietario = new Quill('#editor_proprietario', {
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

        // Inicializa o editor para a energia
        var quillEnergia = new Quill('#editor_energia', {
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

        // Inicializa o editor para a inscricao
        var quillInscricao = new Quill('#editor_inscricao', {
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

        // Inicializa o editor para a agua
        var quillAgua = new Quill('#editor_agua', {
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


        // Inicializa o editor para a condominio
        var quillCondominio = new Quill('#editor_condominio', {
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
        // Inicializa o editor para a TV por assinatura
        var quillTv = new Quill('#editor_tv', {
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

        // Inicializa o editor para a gas
        var quillGas = new Quill('#editor_gas', {
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
        // Inicializa o editor para a Internet
        var quillInternet = new Quill('#editor_internet', {
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
            var proprietarioTextarea = document.getElementById('proprietario_imovel');
            var energiaTextarea = document.getElementById('energia_imovel');
            var inscricaoTextarea = document.getElementById('inscricao_imovel');
            var aguaTextarea = document.getElementById('agua_imovel');
            var condominioTextarea = document.getElementById('condominio_imovel');
            var tvTextarea = document.getElementById('tv_imovel');
            var gasTextarea = document.getElementById('gas_imovel');
            var internetTextarea = document.getElementById('internet_imovel');


            if (proprietarioTextarea && proprietarioTextarea.value) {
                // Use `dangerouslyPasteHTML` para carregar HTML no Quill
                quillProprietario.clipboard.dangerouslyPasteHTML(proprietarioTextarea.value);
            }
            if (energiaTextarea && energiaTextarea.value) {
                quillEnergia.clipboard.dangerouslyPasteHTML(energiaTextarea.value);
            }
            if (inscricaoTextarea && inscricaoTextarea.value) {
                // Use `dangerouslyPasteHTML` para carregar HTML no Quill
                quillInscricao.clipboard.dangerouslyPasteHTML(inscricaoTextarea.value);
            }
            if (aguaTextarea && aguaTextarea.value) {
                quillAgua.clipboard.dangerouslyPasteHTML(aguaTextarea.value);
            }
            if (condominioTextarea && condominioTextarea.value) {
                // Use `dangerouslyPasteHTML` para carregar HTML no Quill
                quillCondominio.clipboard.dangerouslyPasteHTML(condominioTextarea.value);
            }
            if (tvTextarea && tvTextarea.value) {
                quillTv.clipboard.dangerouslyPasteHTML(tvTextarea.value);
            }
            if (gasTextarea && gasTextarea.value) {
                // Use `dangerouslyPasteHTML` para carregar HTML no Quill
                quillGas.clipboard.dangerouslyPasteHTML(gasTextarea.value);
            }
            if (internetTextarea && internetTextarea.value) {
                quillInternet.clipboard.dangerouslyPasteHTML(internetTextarea.value);
            }
        });

        // --- Atualizar o textarea oculto com o conteúdo do Quill antes do envio do formulário ---
        var meuFormulario = document.getElementById('imForm'); // Use o ID do seu formulário

        if (meuFormulario) {
            meuFormulario.addEventListener('submit', function() {
                document.getElementById('proprietario_imovel').value = quillProprietario.root.innerHTML;
                document.getElementById('energia_imovel').value = quillEnergia.root.innerHTML;
                document.getElementById('inscricao_imovel').value = quillInscricao.root.innerHTML;
                document.getElementById('agua_imovel').value = quillAgua.root.innerHTML;
                document.getElementById('condominio_imovel').value = quillCondominio.root.innerHTML;
                document.getElementById('tv_imovel').value = quillTv.root.innerHTML;
                document.getElementById('gas_imovel').value = quillGas.root.innerHTML;
                document.getElementById('internet_imovel').value = quillInternet.root.innerHTML;
            });
        } else {
            console.warn("Formulário com ID 'imForm' não encontrado. Certifique-se de que o Quill está sendo atualizado antes do envio.");
        }
    </script>
</body>

</html>