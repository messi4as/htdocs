<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISUALIZAÇÃO DO INVENTÁRIO DE ATIVOS</title>

    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        /* Estilo para a imagem na tabela principal que é clicável (Lightbox) */
        .foto-lista-clicavel {
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .foto-lista-clicavel:hover {
            opacity: 0.8;
        }
        
        /* Oculta a última coluna (AÇÕES, que estará vazia) e elementos não necessários na impressão */
        @media print {
            .pagination, .modal, .card-header .row { 
                display: none !important;
            }
            body { 
                margin: 0; 
            }
            /* Garante que a coluna AÇÕES (agora vazia) não apareça na impressão */
            .table thead th:last-child, .table tbody tr td:last-child {
                display: none !important;
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
                            <h2 class="mb-4">  <a href="/inventario/inventario.php">📋</a> VISUALIZAÇÃO DO INVENTÁRIO DE ATIVOS M2 SHOWS
                                  <div class="float-end">
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
                                            <!-- <th width="120">AÇÕES</th> -->
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
        // UPLOAD_DOCS_PATH não é necessário, pois a aba Docs foi removida/não será usada.

        // VARIÁVEIS GLOBAIS DE PAGINAÇÃO
        const ITENS_POR_PAGINA = 50; 
        let paginaAtual = 1;

        $(document).ready(function() {
            carregarLista(1); 

            // Filtros automáticos: aciona o carregamento da lista em qualquer alteração
            $('#filtroLocal, #filtroTipo, #filtroAmbiente, #checkTodos').on('change keyup', function() {
                carregarLista(1); 
            });
        });

        // ----------------------------------------------------------------------
        // FUNÇÕES DE LISTAGEM E PAGINAÇÃO 
        // ----------------------------------------------------------------------

        function carregarLista(pagina = 1) {
            paginaAtual = pagina; 
            const offset = (paginaAtual - 1) * ITENS_POR_PAGINA; 
            
            $.post('ajax_inventario.php', {
                acao: 'listar',
                local: $('#filtroLocal').val(),
                tipo: $('#filtroTipo').val(),
                ambiente: $('#filtroAmbiente').val(),
                todos: $('#checkTodos').is(':checked') ? 'true' : 'false',
                limite: ITENS_POR_PAGINA,
                offset: offset,
                // O NOVO PARÂMETRO CHAVE para sinalizar ao PHP que a lista é SÓ LEITURA
                somente_leitura: 'true' 
            }, function(data) {
                if(data.status === 'ok') {
                    $('#listaItens').html(data.html); 
                    montarPaginacao(data.total_itens); 
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
            let startPage = Math.max(1, paginaAtual - 2);
            let endPage = Math.min(totalPaginas, paginaAtual + 2);
            
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
            
            let paginaInfo = totalItens > 0 ? `(Página ${paginaAtual} de ${totalPaginas}. Total: ${totalItens} itens)` : '';
            $('caption').text(`ITENS ATIVOS NO INVENTÁRIO ${paginaInfo}`);
        }

        // ----------------------------------------------------------------------
        // FUNÇÃO DE IMPRESSÃO (Mantida, mas não envia o parâmetro de somente leitura, o que está correto pois o PHP já trata 'imprimir_lista' sem botões)
        // ----------------------------------------------------------------------

        function imprimirListaCompleta() {
            // 1. Coleta os filtros
            const filtros = {
                acao: 'imprimir_lista', 
                local: $('#filtroLocal').val(),
                tipo: $('#filtroTipo').val(),
                ambiente: $('#filtroAmbiente').val(),
                todos: $('#checkTodos').is(':checked') ? 'true' : 'false'
                // Não é necessário enviar 'somente_leitura' aqui, pois a ação 'imprimir_lista' no PHP já garante que a coluna AÇÕES é vazia.
            };

            // 2. Requisição AJAX para obter o HTML completo (sem paginação)
            $.post('ajax_inventario.php', filtros, function(fullHtml) {
                if (!fullHtml.trim().startsWith('<tr')) {
                    alert("Erro ao gerar lista para impressão ou lista vazia.");
                    return;
                }

                // 3. Monta o Conteúdo HTML completo para impressão
                const titulo = `INVENTÁRIO DE ATIVOS M2 SHOWS - Lista Filtrada (${new Date().toLocaleDateString()})`;
                
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
                            .table tr.table-danger { background-color: #f8d7da !important; color: #842029 !important; }
                            .table small { font-size: 0.8em; } 
                            /* Oculta a última coluna de AÇÕES na impressão (igual ao CSS global)*/
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
                
                printWindow.onload = function() {
                    printWindow.focus(); 
                    printWindow.print();
                };

            }, 'html').fail(function(xhr, status, error) {
                alert("Erro na requisição da lista completa para impressão. Resposta: " + xhr.responseText);
            });
        }


        // ----------------------------------------------------------------------
        // CÓDIGO PARA VISUALIZAÇÃO INTERATIVA (LIGHTBOX) - MANTIDO
        // ----------------------------------------------------------------------

        function visualizarFotosItem(itemId, fotoInicialId = null) {
            $.getJSON('ajax_inventario.php', {
                acao: 'get_fotos_item', 
                id: itemId
            }, function(data) {
                if (data.status === 'ok' && data.fotos && data.fotos.length > 0) {
                    let carouselInner = '';
                    let startIndex = 0; 
                    let totalFotos = data.fotos.length;

                    $.each(data.fotos, function(index, foto) {
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

                    $('#carouselFotosModal .carousel-inner').html(carouselInner);

                    var carouselEl = document.getElementById('carouselFotosModal');
                    var carousel = bootstrap.Carousel.getInstance(carouselEl) || new bootstrap.Carousel(carouselEl, {
                        interval: false
                    });
                    carousel.to(startIndex); 

                    $('#modalVisualizarFoto').off('slid.bs.carousel').on('slid.bs.carousel', function() {
                        let currentIndex = $('#carouselFotosModal .carousel-item.active').index();
                        $('#fotoAtualInfo').text(`Foto ${currentIndex + 1} de ${totalFotos}`);
                    }).trigger('slid.bs.carousel'); 

                    $('#modalVisualizarFoto').modal('show');

                } else {
                    alert('Nenhuma foto encontrada para este item.');
                }
            }).fail(function() {
                alert("Erro ao buscar as fotos do item. Verifique a ação 'get_fotos_item' no ajax_inventario.php.");
            });
        }

        // O click na FOTO DE CAPA na LISTAGEM PRINCIPAL é MANTIDO
        $(document).on('click', '.foto-lista-clicavel', function() {
            let itemId = $(this).data('item-id');
            // Como é o clique na lista, passamos NULL para fotoInicialId, assim ele mostra a capa (índice 0)
            visualizarFotosItem(itemId);
        });
        
        // O click de miniaturas da GALERIA DE EDIÇÃO foi removido, pois o modal de edição foi removido.
    </script>

</body>

</html>