<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INVENTÁRIO DE ATIVOS</title>

    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Estilos para a galeria de fotos e documentos */

        .table img {
            object-fit: cover;
            border: 1px solid #ddd;
        }

        .foto-container {
            position: relative;
            margin-bottom: 15px;
            border: 2px solid transparent;
            border-radius: .25rem;
            padding: 5px;
            transition: all 0.2s ease-in-out;
        }

        /* Destaque visual para a foto de capa */
        .foto-container.capa {
            border-color: #007bff;
            /* Azul para a capa */
            box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        }

        .foto-container img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: .25rem;
            cursor: pointer;
            /* Indica que a foto pode ser clicada (para definir capa ou abrir lightbox) */
        }

        .foto-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.6);
            color: white;
            text-align: center;
            padding: 5px 0;
            border-bottom-left-radius: .25rem;
            border-bottom-right-radius: .25rem;
            opacity: 0;
            transition: opacity 0.2s ease-in-out;
            display: flex;
            justify-content: space-around;
            align-items: center;
        }

        .foto-container:hover .foto-overlay {
            opacity: 1;
        }

        .foto-overlay .btn {
            font-size: 0.8rem;
            padding: 3px 6px;
        }

        .doc-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: .25rem;
        }

        .doc-item i {
            margin-right: 8px;
        }

        /* Estilo para a imagem na tabela principal que agora é clicável */
        .foto-lista-clicavel {
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .foto-lista-clicavel:hover {
            opacity: 0.8;
        }
        
        /* Oculta elementos que não devem ser impressos */
        @media print {
            .btn-editar, .btn-mover, .btn-reverter, .pagination, .modal, .card-header .row { 
                display: none !important;
            }
            body { 
                margin: 0; 
            }
        }
    </style>
</head>

<body>

    <?php include('/xampp/htdocs/navbar.php'); ?>


    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="container-fluid py-4">
                            <h2 class="mb-4">  📋</a>INVENTÁRIO DE ATIVOS M2 SHOWS
                                  <div class="float-end">

                                <button class="btn btn-primary" onclick="abrirModalCadastro()">
                                    <i class="fa fa-plus"></i> Novo Item
                                </button>
                                <button class="btn btn-info" onclick="imprimirListaCompleta()" title="Imprimir Lista Filtrada Completa">
                                    <i class="fa fa-print"></i> Imprimir Lista
                                </button>
                            </div>
                                </h2>
                        </div>
                        

                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <div class="row align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label"><strong>LOCAL</strong></label>
                                            <input type="text" id="filtroLocal" class="form-control"
                                                placeholder="Ex: ESCRITÓRIO M2">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label"><strong>TIPO</strong></label>
                                            <input type="text" id="filtroTipo" class="form-control"
                                                placeholder="Ex: MÓVEIS">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label"><strong>AMBIENTE</strong></label>
                                            <input type="text" id="filtroAmbiente" class="form-control"
                                                placeholder="Ex: SALA FINANCEIRO">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="checkTodos">
                                                <label class="form-check-label" for="checkTodos"><strong>Exibir
                                                        Vendidos/Baixados</strong></label>
                                            </div>
                                        </div>


                                    </div>
                                </div>

                            </div>


                            <div class="table-responsive">
                                <table class="table table-hover align-middle caption-top">
                                    <caption><strong>ITENS ATIVOS NO INVENTÁRIO</strong></caption>

                                    <thead class="table-light">

                                        <tr class="text-center align-middle">
                                            <th>FOTO</th>
                                            <th>ID</th>
                                            <th>ITEM / TIPO</th>
                                            <th>LOCAL / AMBIENTE</th>
                                            <th>DATA DE CADASTRO</th>
                                            <th>STATUS</th>
                                            <th width="120">AÇÕES</th>
                                        </tr>
                                    </thead>
                                    <tbody id="listaItens">
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">Carregando itens...</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="row mt-3">
                                    <div class="col-12">
                                        <nav>
                                            <ul class="pagination justify-content-center" id="paginacao">
                                                </ul>
                                        </nav>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalCad" tabindex="-1" aria-labelledby="modalCadLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formItem" enctype="multipart/form-data">
                    <input type="hidden" name="acao" value="salvar">
                    <input type="hidden" name="id" id="itemId">

                    <div class="modal-header">
                        <h5 class="modal-title" id="modalCadLabel">Gerenciar Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#dados"
                                    role="tab">Dados</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#fotos"
                                    role="tab">Fotos</a></li>
                            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#docs"
                                    role="tab">Documentos</a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="dados" role="tabpanel">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Nome do Item</label>
                                        <input type="text" name="nome" id="nome" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Tipo</label>
                                        <input type="text" name="tipo" id="tipo" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Local (Escritório/Fazenda/Prédio)</label>
                                        <input type="text" name="local_nome" id="local_nome" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Ambiente (Sada de Reunião/Sala Financeiro)</label>
                                        <input type="text" name="ambiente" id="ambiente" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Descrição</label>
                                        <textarea name="descricao" id="descricao" class="form-control"
                                            rows="3"></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="fotos" role="tabpanel">
                                <label class="form-label">Adicionar Novas Fotos (Permite múltiplas)</label>
                                <input type="file" name="fotos[]" class="form-control mb-3" multiple accept="image/*">
                                <hr>
                                <h6>🖼️ Fotos Atuais (Defina a capa ou exclua)</h6>
                                <div id="galeriaPreview" class="row">
                                    <p class="text-muted" id="noFotosMsg">Nenhuma foto adicionada ainda.</p>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="docs" role="tabpanel">
                                <label class="form-label">Anexar Novo Documento (Pode ser um substituto)</label>
                                <input type="file" name="doc" class="form-control mb-3">
                                <hr>
                                <h6>📎 Documentos Anexados</h6>
                                <div id="documentosPreview">
                                    <p class="text-muted" id="noDocsMsg">Nenhum documento anexado ainda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalMover" tabindex="-1" aria-labelledby="modalMoverLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="modalMoverLabel">Mover / Baixar Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="moveItemId">
                    <div class="mb-3">
                        <label class="form-label">Novo Status</label>
                        <select id="moveStatus" class="form-select">
                            <option value="vendido">Vendido</option>
                            <option value="doado">Doado</option>
                            <option value="extraviado">Extraviado</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivo / Observação</label>
                        <textarea id="moveMotivo" class="form-control" rows="3"
                            placeholder="Ex: Vendido para: Fulano por R$ 500 ou Doado para: Beltrano"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="confirmarMover()">Confirmar Baixa</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVisualizarFoto" tabindex="-1" aria-labelledby="modalVisualizarFotoLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content bg-dark text-white">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="modalVisualizarFotoLabel">Visualização de Fotos</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <div id="carouselFotosModal" class="carousel slide" data-bs-interval="false">
                        <div class="carousel-inner">
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselFotosModal"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselFotosModal"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <span id="fotoAtualInfo"></span>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Caminho de upload, deve ser o mesmo usado no PHP
        const UPLOAD_FOTOS_PATH = 'uploads/fotos/';
        const UPLOAD_DOCS_PATH = 'uploads/docs/';

        // VARIÁVEIS GLOBAIS DE PAGINAÇÃO
        const ITENS_POR_PAGINA = 50; // Defina um limite razoável
        let paginaAtual = 1;

        $(document).ready(function() {
            // Carrega a lista inicial dos itens
            carregarLista(1); // Inicia na página 1

            // Filtros automáticos: aciona o carregamento da lista em qualquer alteração
            $('#filtroLocal, #filtroTipo, #filtroAmbiente, #checkTodos').on('change keyup', function() {
                carregarLista(1); // SEMPRE volta para a primeira página ao filtrar
            });

            // Submissão do Formulário de Cadastro/Edição
            $('#formItem').on('submit', function(e) {
                e.preventDefault();
                var formData = new FormData(this);

                $.ajax({
                    url: 'ajax_inventario.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(resp) {
                        if (resp.status === 'ok') {
                            $('#modalCad').modal('hide');
                            carregarLista(paginaAtual); // Atualiza a lista principal (na página atual)
                            alert('Item salvo com sucesso!');
                        } else {
                            alert('Erro ao salvar item: ' + (resp.msg || 'Erro desconhecido.'));
                        }
                    },
                    error: function(xhr, status, error) {
                        alert("Erro ao salvar o item. Verifique o console ou o arquivo ajax_inventario.php. Resposta: " + xhr.responseText);
                    }
                });
            });
        });

        // ----------------------------------------------------------------------
        // FUNÇÕES DE LISTAGEM E PAGINAÇÃO (Atualizadas)
        // ----------------------------------------------------------------------

        function carregarLista(pagina = 1) {
            paginaAtual = pagina; // Armazena a página atual

            // Calcula o offset (onde a busca deve começar)
            const offset = (paginaAtual - 1) * ITENS_POR_PAGINA; 
            
            $.post('ajax_inventario.php', {
                acao: 'listar',
                local: $('#filtroLocal').val(),
                tipo: $('#filtroTipo').val(),
                ambiente: $('#filtroAmbiente').val(),
                todos: $('#checkTodos').is(':checked') ? 'true' : 'false',
                // NOVOS PARÂMETROS ENVIADOS AO PHP
                limite: ITENS_POR_PAGINA,
                offset: offset
            }, function(data) {
                // O PHP deve retornar JSON com dados da paginação e o HTML
                if(data.status === 'ok') {
                    $('#listaItens').html(data.html); // HTML da tabela
                    montarPaginacao(data.total_itens); // Função para montar os botões
                } else {
                    $('#listaItens').html('<tr><td colspan="7" class="text-center text-danger">Erro: ' + data.msg + '</td></tr>');
                }
            }, 'json').fail(function(xhr, status, error) {
                $('#listaItens').html('<tr><td colspan="7" class="text-center text-danger">Erro ao carregar lista (AJAX). Resposta: ' + xhr.responseText + '</td></tr>');
            });
        }

        /**
         * Monta os botões de paginação
         * @param {number} totalItens O número total de itens no inventário com os filtros atuais.
         */
        function montarPaginacao(totalItens) {
            const totalPaginas = Math.ceil(totalItens / ITENS_POR_PAGINA);
            let paginacaoHtml = '';

            // Botão Anterior
            paginacaoHtml += `<li class="page-item ${paginaAtual === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="carregarLista(${paginaAtual - 1})">Anterior</a>
            </li>`;

            // Links de Páginas (ex: 1, 2, 3...)
            // Limita para mostrar apenas algumas páginas ao redor da atual
            let startPage = Math.max(1, paginaAtual - 2);
            let endPage = Math.min(totalPaginas, paginaAtual + 2);
            
            // Garante que pelo menos 5 páginas sejam mostradas se houver
            if (totalPaginas > 5) {
                if (endPage - startPage < 4) {
                     startPage = Math.max(1, endPage - 4);
                }
            }

            for (let i = startPage; i <= endPage; i++) {
                paginacaoHtml += `<li class="page-item ${i === paginaAtual ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="carregarLista(${i})">${i}</a>
                </li>`;
            }

            // Botão Próxima
            paginacaoHtml += `<li class="page-item ${paginaAtual === totalPaginas || totalItens === 0 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="carregarLista(${paginaAtual + 1})">Próxima</a>
            </li>`;

            $('#paginacao').html(paginacaoHtml);
            
            // Atualiza a legenda da tabela
            let paginaInfo = totalItens > 0 ? `(Página ${paginaAtual} de ${totalPaginas}. Total: ${totalItens} itens)` : '';
            $('caption').text(`ITENS ATIVOS NO INVENTÁRIO ${paginaInfo}`);
        }

        // ----------------------------------------------------------------------
        // NOVO: Função para Imprimir a Lista Completa Filtrada
        // ----------------------------------------------------------------------

        function imprimirListaCompleta() {
            // 1. Coleta os filtros
            const filtros = {
                acao: 'imprimir_lista', // Chama a nova ação que retorna o HTML completo
                local: $('#filtroLocal').val(),
                tipo: $('#filtroTipo').val(),
                ambiente: $('#filtroAmbiente').val(),
                todos: $('#checkTodos').is(':checked') ? 'true' : 'false'
            };

            // 2. Requisição AJAX para obter o HTML completo (sem paginação)
            $.post('ajax_inventario.php', filtros, function(fullHtml) {
                if (!fullHtml.trim().startsWith('<tr')) {
                    // Se o retorno não for o início de uma linha <tr>, assume que é uma mensagem de erro ou vazia.
                    alert("Erro ao gerar lista para impressão ou lista vazia.");
                    return;
                }

                // 3. Monta o Conteúdo HTML completo para impressão
                const titulo = `INVENTÁRIO DE ATIVOS M2 SHOWS - Lista Filtrada (${new Date().toLocaleDateString()})`;
                
                // Define o cabeçalho da tabela de impressão
                const tableHeader = `
                    <thead style="background-color:#f8f9fa;">
                        <tr style="text-align: center; vertical-align: middle;">
                            <th>FOTO</th>
                            <th>ID</th>
                            <th>ITEM / TIPO</th>
                            <th>LOCAL / AMBIENTE</th>
                            <th>DATA DE CADASTRO</th>
                            <th>STATUS</th>
                            <th width="120">AÇÕES</th>
                        </tr>
                    </thead>
                `;

                const printContent = `
                    <html>
                    <head>
                        <title>${titulo}</title>
                        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                        <style>
                            body { font-family: sans-serif; padding: 20px; }
                            .print-header { text-align: center; margin-bottom: 20px; }
                            .table { width: 100%; border-collapse: collapse; }
                            .table th, .table td { border: 1px solid #ccc; padding: 8px; font-size: 11px; }
                            .table img { width: 50px; height: 50px; object-fit: cover; border-radius: .25rem; }
                            /* Estilo para linhas de itens baixados (se for o caso) */
                            .table tr.table-danger { background-color: #f8d7da !important; color: #842029 !important; }
                            .table small { font-size: 0.8em; } 
                            /* Oculta a última coluna de AÇÕES na impressão */
                            .table thead th:last-child, .table tbody tr td:last-child {
                                display: none !important;
                            }
                        </style>
                    </head>
                    <body>
                        <h3 class="print-header">${titulo}</h3>
                        <p><strong>Filtros Aplicados:</strong> Local: ${$('#filtroLocal').val() || 'Todos'} | Tipo: ${$('#filtroTipo').val() || 'Todos'} | Ambiente: ${$('#filtroAmbiente').val() || 'Todos'} | Incluindo Baixados: ${$('#checkTodos').is(':checked') ? 'Sim' : 'Não'}</p>
                        <table class="table table-bordered table-striped">
                            ${tableHeader}
                            <tbody>
                                ${fullHtml}
                            </tbody>
                        </table>
                    </body>
                    </html>
                `;

                // 4. Abre Nova Janela, Escreve Conteúdo e Imprime
                const printWindow = window.open('', '_blank');
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                // Aguarda o carregamento e dispara a impressão
                printWindow.onload = function() {
                    printWindow.focus(); 
                    printWindow.print();
                    // printWindow.close(); // Opcional
                };

            }, 'html').fail(function(xhr, status, error) {
                alert("Erro na requisição da lista completa para impressão. Resposta: " + xhr.responseText);
            });
        }


        // ----------------------------------------------------------------------
        // FUNÇÕES DE MODAL (Mantidas)
        // ----------------------------------------------------------------------

        function abrirModalCadastro() {
            $('#formItem')[0].reset();
            $('#itemId').val('');
            $('#modalCadLabel').text('Cadastrar Novo Item');
            $('#myTab a[href="#dados"]').tab('show');

            // --- CORREÇÃO DE LIMPEZA DE INPUT FILE (IMPEDE DUPLICAÇÃO) ---
            $('input[name="fotos[]"]').val('');
            $('input[name="doc"]').val('');
            // -----------------------------------------------------------

            $('#galeriaPreview').html('<p class="text-muted" id="noFotosMsg">Nenhuma foto adicionada ainda.</p>');
            $('#documentosPreview').html('<p class="text-muted" id="noDocsMsg">Nenhum documento anexado ainda.</p>');

            $('#modalCad').modal('show');
        }

        // Lógica para o botão EDITAR
        $(document).on('click', '.btn-editar', function() {
            let id = $(this).data('id');
            $('#itemId').val(id);
            $('#modalCadLabel').text('Editar Item ID: ' + id);
            $('#myTab a[href="#dados"]').tab('show');

            // --- CORREÇÃO DE LIMPEZA DE INPUT FILE (IMPEDE DUPLICAÇÃO) ---
            $('input[name="fotos[]"]').val('');
            $('input[name="doc"]').val('');
            // -----------------------------------------------------------

            // Limpa galerias e documentos visuais
            $('#galeriaPreview').html('');
            $('#documentosPreview').html('');

            // Faz a requisição AJAX para buscar os dados do item
            $.getJSON('ajax_inventario.php', {
                acao: 'get_item',
                id: id
            }, function(data) {
                if (data.item) {
                    // Preenche os dados principais
                    $('#nome').val(data.item.nome);
                    $('#tipo').val(data.item.tipo);
                    $('#local_nome').val(data.item.local_nome);
                    $('#ambiente').val(data.item.ambiente);
                    $('#descricao').val(data.item.descricao);

                    // Carrega as fotos
                    if (data.fotos && data.fotos.length > 0) {
                        let fotosHtml = '';
                        $.each(data.fotos, function(index, foto) {
                            let isCapa = foto.eh_capa == 1 ? 'capa' : '';
                            let btnCapaClass = foto.eh_capa == 1 ? 'btn-primary' : 'btn-outline-primary';
                            let iconCapa = foto.eh_capa == 1 ? 'fa-star' : 'fa-star-o';

                            // A IMAGEM DA GALERIA AGORA PODE SER CLICADA PARA ABRIR O LIGHTBOX
                            fotosHtml += `
                                <div class="col-auto foto-container ${isCapa}" data-foto-id="${foto.id}">
                                    <img src="${UPLOAD_FOTOS_PATH}${foto.arquivo}" alt="Foto ${foto.id}" class="foto-galeria-clicavel">
                                    <div class="foto-overlay">
                                        <button class="btn ${btnCapaClass} btn-capa" data-foto-id="${foto.id}" title="Definir como Capa">
                                            <i class="fa ${iconCapa}"></i> Capa
                                        </button>
                                        <button class="btn btn-danger btn-remover-foto" data-foto-id="${foto.id}" title="Remover Foto">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        $('#galeriaPreview').html(fotosHtml);
                    } else {
                        $('#galeriaPreview').html('<p class="text-muted" id="noFotosMsg">Nenhuma foto adicionada ainda.</p>');
                    }

                    // Carrega os documentos
                    if (data.docs && data.docs.length > 0) {
                        let docsHtml = '';
                        $.each(data.docs, function(index, doc) {
                            docsHtml += `
                                <div class="doc-item" data-doc-id="${doc.id}">
                                    <span><i class="fa fa-file-alt"></i> ${doc.nome_doc}</span>
                                    <div>
                                        <a href="${UPLOAD_DOCS_PATH}${doc.arquivo}" target="_blank" class="btn btn-info btn-sm" title="Visualizar/Baixar"><i class="fa fa-eye"></i></a>
                                        <button class="btn btn-danger btn-sm btn-remover-doc" data-doc-id="${doc.id}" title="Remover"><i class="fa fa-trash"></i></button>
                                    </div>
                                </div>
                            `;
                        });
                        $('#documentosPreview').html(docsHtml);
                    } else {
                        $('#documentosPreview').html('<p class="text-muted" id="noDocsMsg">Nenhum documento anexado ainda.</p>');
                    }

                    $('#modalCad').modal('show');
                } else {
                    alert('Erro ao carregar dados do item.');
                }
            }).fail(function(xhr, status, error) {
                alert("Erro ao buscar dados para edição. Verifique o console. Resposta: " + xhr.responseText);
            });
        });

        // Lógica para o botão MOVER/BAIXAR
        $(document).on('click', '.btn-mover', function() {
            let id = $(this).data('id');
            $('#moveItemId').val(id);
            $('#moveStatus').val('vendido'); // Status padrão
            $('#moveMotivo').val('');
            $('#modalMover').modal('show');
        });

        function confirmarMover() {
            let id = $('#moveItemId').val();
            let status = $('#moveStatus').val();
            let motivo = $('#moveMotivo').val();

            if (motivo.trim() === '') {
                alert('Por favor, informe o Motivo da Baixa.');
                return;
            }

            $.post('ajax_inventario.php', {
                acao: 'mover',
                id: id,
                novo_status: status,
                motivo: motivo
            }, function(response) {
                $('#modalMover').modal('hide');
                carregarLista(paginaAtual); // Recarrega a página atual
            }).fail(function(xhr, status, error) {
                alert("Erro na requisição para mover/baixar item: " + xhr.responseText);
            });
        }

        // ----------------------------------------------------------------------
        // AÇÕES DE ANEXOS E CAPA
        // ----------------------------------------------------------------------

        // Ação de Definir Capa
        $(document).on('click', '.btn-capa', function() {
            let fotoId = $(this).data('foto-id');
            let itemId = $('#itemId').val(); // Pega o ID do item que está sendo editado

            $.post('ajax_inventario.php', {
                acao: 'definir_capa',
                item_id: itemId,
                foto_id: fotoId
            }, function(resp) {
                if (resp.status === 'ok') {
                    // Atualização visual imediata na galeria
                    $('#galeriaPreview .foto-container').removeClass('capa');
                    $('#galeriaPreview .btn-capa').removeClass('btn-primary').addClass('btn-outline-primary').find('i').removeClass('fa-star').addClass('fa-star-o');

                    $(`.foto-container[data-foto-id="${fotoId}"]`).addClass('capa');
                    $(`.btn-capa[data-foto-id="${fotoId}"]`).removeClass('btn-outline-primary').addClass('btn-primary').find('i').removeClass('fa-star-o').addClass('fa-star');

                    carregarLista(paginaAtual); // Atualiza a lista principal para mostrar a nova capa
                } else {
                    alert('Erro ao definir capa: ' + (resp.msg || 'Erro desconhecido.'));
                }
            }, 'json').fail(function(xhr, status, error) {
                alert("Erro na requisição para definir capa: " + xhr.responseText);
            });
        });

        // Ação de Remover Foto
        $(document).on('click', '.btn-remover-foto', function() {
            if (!confirm('Tem certeza que deseja remover esta foto? O arquivo será EXCLUÍDO do servidor.')) return;

            let fotoId = $(this).data('foto-id');
            let itemId = $('#itemId').val();

            $.post('ajax_inventario.php', {
                acao: 'remover_foto',
                foto_id: fotoId,
                item_id: itemId
            }, function(resp) {
                if (resp.status === 'ok') {
                    // Remove a miniatura da tela e recarrega a lista
                    $(`.foto-container[data-foto-id="${fotoId}"]`).remove();
                    carregarLista(paginaAtual);
                    // Opcional: Verifica se não há mais fotos e mostra a mensagem de "Nenhuma foto..."
                    if ($('#galeriaPreview').children().length === 0) {
                        $('#galeriaPreview').html('<p class="text-muted" id="noFotosMsg">Nenhuma foto adicionada ainda.</p>');
                    }
                } else {
                    alert('Erro ao remover foto: ' + (resp.msg || 'Erro desconhecido.'));
                }
            }, 'json').fail(function(xhr, status, error) {
                alert("Erro na requisição para remover foto: " + xhr.responseText);
            });
        });

        // Ação de Remover Documento
        $(document).on('click', '.btn-remover-doc', function() {
            if (!confirm('Tem certeza que deseja remover este documento? O arquivo será EXCLUÍDO do servidor.')) return;

            let docId = $(this).data('doc-id');

            $.post('ajax_inventario.php', {
                acao: 'remover_doc',
                doc_id: docId
            }, function(resp) {
                if (resp.status === 'ok') {
                    // Remove o item da lista
                    $(`.doc-item[data-doc-id="${docId}"]`).remove();
                    // Opcional: Verifica se não há mais docs e mostra a mensagem de "Nenhum..."
                    if ($('#documentosPreview').children().length === 0) {
                        $('#documentosPreview').html('<p class="text-muted" id="noDocsMsg">Nenhum documento anexado ainda.</p>');
                    }
                } else {
                    alert('Erro ao remover documento: ' + (resp.msg || 'Erro desconhecido.'));
                }
            }, 'json').fail(function(xhr, status, error) {
                alert("Erro na requisição para remover documento: " + xhr.responseText);
            });
        });


        // ----------------------------------------------------------------------
        // AÇÃO DE REVERTER BAIXA
        // ----------------------------------------------------------------------

        // Lógica para o botão REVERTER BAIXA
        $(document).on('click', '.btn-reverter', function() {
            let id = $(this).data('id');
            reverterItem(id);
        });

        /**
         * Função para reverter a baixa de um item.
         */
        function reverterItem(id) {
            if (!confirm('Tem certeza que deseja reverter a baixa do Item ID ' + id + '? Ele voltará a ter o status "Ativo".')) return;

            $.post('ajax_inventario.php', {
                acao: 'reverter_baixa',
                id: id
            }, function(resp) {
                if (resp.status === 'ok') {
                    alert('Item ID ' + id + ' revertido para Ativo com sucesso!');
                    carregarLista(paginaAtual); // Recarrega a lista para atualizar os botões e a linha
                } else {
                    alert('Erro ao reverter item: ' + (resp.msg || 'Erro desconhecido.'));
                }
            }, 'json').fail(function(xhr, status, error) {
                alert("Erro na requisição para reverter item: " + xhr.responseText);
            });
        }


        // ----------------------------------------------------------------------
        // CÓDIGO PARA VISUALIZAÇÃO INTERATIVA (LIGHTBOX)
        // ----------------------------------------------------------------------

        /**
         * Função para buscar as fotos do item e montar o carrossel no modal.
         */
        function visualizarFotosItem(itemId, fotoInicialId = null) {
            // 1. Faz a requisição AJAX para buscar todas as fotos
            $.getJSON('ajax_inventario.php', {
                acao: 'get_fotos_item', 
                id: itemId
            }, function(data) {
                if (data.status === 'ok' && data.fotos && data.fotos.length > 0) {
                    let carouselInner = '';
                    let startIndex = 0; // Índice da foto a ser mostrada primeiro
                    let totalFotos = data.fotos.length;

                    // 2. Monta o HTML do carrossel
                    $.each(data.fotos, function(index, foto) {
                        // Se um fotoInicialId for passado, encontramos o índice dela
                        if (fotoInicialId && foto.id == fotoInicialId) {
                            startIndex = index;
                        }

                        let activeClass = index === startIndex ? 'active' : '';
                        let imageUrl = UPLOAD_FOTOS_PATH + foto.arquivo;

                        carouselInner += `
                            <div class="carousel-item ${activeClass}">
                                <img src="${imageUrl}" class="d-block w-100" alt="Foto ${index + 1}" style="max-height: 80vh; object-fit: contain;">
                            </div>
                        `;
                    });

                    // 3. Insere o carrossel no modal
                    $('#carouselFotosModal .carousel-inner').html(carouselInner);

                    // 4. Configura o carrossel para começar na foto correta
                    // Garante que o carrossel é reinicializado se estiver aberto
                    var carouselEl = document.getElementById('carouselFotosModal');
                    var carousel = bootstrap.Carousel.getInstance(carouselEl) || new bootstrap.Carousel(carouselEl, {
                        interval: false
                    });
                    carousel.to(startIndex); // Navega para o índice da foto clicada/capa

                    // 5. Atualiza o contador de fotos
                    $('#modalVisualizarFoto').off('slid.bs.carousel').on('slid.bs.carousel', function() {
                        let currentIndex = $('#carouselFotosModal .carousel-item.active').index();
                        $('#fotoAtualInfo').text(`Foto ${currentIndex + 1} de ${totalFotos}`);
                    }).trigger('slid.bs.carousel'); // Dispara para atualizar na abertura

                    // 6. Exibe o modal
                    $('#modalVisualizarFoto').modal('show');

                } else {
                    alert('Nenhuma foto encontrada para este item.');
                }
            }).fail(function() {
                alert("Erro ao buscar as fotos do item. Verifique a ação 'get_fotos_item' no ajax_inventario.php.");
            });
        }

        // 1. Para as miniaturas na GALERIA DO MODAL DE EDIÇÃO
        $(document).on('click', '.foto-galeria-clicavel', function() {
            let itemId = $('#itemId').val(); // Pega o ID do item do modal de edição
            // O foto-container é o pai da imagem
            let fotoId = $(this).closest('.foto-container').data('foto-id');

            // Chama a função para visualizar com a foto clicada como inicial
            visualizarFotosItem(itemId, fotoId);
        });

        // 2. Para a FOTO DE CAPA na LISTAGEM PRINCIPAL
        // Você deve garantir que a tag <img> na lista tenha a classe 'foto-lista-clicavel' 
        // e o 'data-item-id'.
        $(document).on('click', '.foto-lista-clicavel', function() {
            let itemId = $(this).data('item-id');
            // Como é o clique na lista, passamos NULL para fotoInicialId, assim ele mostra a capa (índice 0)
            visualizarFotosItem(itemId);
        });
    </script>

</body>

</html>