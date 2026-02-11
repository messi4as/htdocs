<?php
session_start();
require 'db_connect.php';

// 1. Recuperar os LOTES para o filtro (Alterado de agrupamento para lote)
$lote_query = "SELECT DISTINCT lote FROM bovinos_com_idade WHERE   lote IS NOT NULL AND lote != '' ORDER BY lote ASC";
$lote_result = mysqli_query($conn, $lote_query);
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

    <title>RELATÓRIO POR LOTE - FAZENDA ROSADA</title>

    <style>
        th, td { text-align: center; vertical-align: middle !important; }
        .img-animal-thumb { 
            width: 80px; height: 80px; 
            object-fit: cover; border-radius: 8px; 
            border: 2px solid #ddd; cursor: pointer; 
            transition: 0.3s;
        }
        .img-animal-thumb:hover { transform: scale(1.1); border-color: #0d6efd; }
        .dt-buttons { margin-bottom: 15px; }
        .btn-export { margin-right: 5px; }
        #imgAmpliada { max-width: 100%; height: auto; border-radius: 5px; }
    </style>
</head>

<body>
     <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <?php include('mensagem.php'); ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">
                            <h4>LISTAGEM POR LOTE
                                  <div class="float-end">
                                        <a href="visualizacao_graficos.php" class="btn btn-warning shadow-sm"><span class="bi bi-bar-chart"></span>&nbsp;Ver Gráfico por Grupo</a>
                                  </div>
                            </h4>
                        </div>
            <div class="card-body">
                 
                <form method="GET" class="row g-2 mb-4 p-3 bg-light rounded">
                    <div class="col-md-5">
                        <input type="text" name="brinco" class="form-control" placeholder="Buscar Brincos..." value="<?= htmlspecialchars($_GET['brinco'] ?? '') ?>">
                    </div>
                    <div class="col-md-5">
                        <select name="lote" class="form-select">
                            <option value="">Todos os Lotes</option>
                            <?php 
                            mysqli_data_seek($lote_result, 0);
                            while ($row = mysqli_fetch_assoc($lote_result)): 
                            ?>
                                <option value="<?= $row['lote'] ?>" <?= (isset($_GET['lote']) && $_GET['lote'] == $row['lote']) ? 'selected' : '' ?>>
                                    <?= $row['lote'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary w-100" type="submit">Filtrar</button>
                    </div>
                </form>

                <?php
                $brinco = mysqli_real_escape_string($conn, $_GET['brinco'] ?? '');
                $lote = mysqli_real_escape_string($conn, $_GET['lote'] ?? '');

                $sql = "SELECT * FROM bovinos_com_idade WHERE status IS NOT NULL";
                if ($brinco != '') {
                    $brincos_arr = array_map('trim', explode(',', $brinco));
                    $conds = array_map(fn($b) => "brinco LIKE '%$b%'", $brincos_arr);
                    $sql .= " AND (" . implode(' OR ', $conds) . ")";
                }
                if ($lote != '') $sql .= " AND lote = '$lote'";
                $sql .= " ORDER BY brinco ASC";
                
                $result = mysqli_query($conn, $sql);
                $quantidade = mysqli_num_rows($result);
                ?>

                <div class="alert alert-info" role="alert">
                    Quantidade de Bovinos **Ativos** Encontrados: <strong><?php echo number_format($quantidade, 0, ',', '.'); ?></strong>
                </div>

                <table id="tabelaBovinos" class="table table-bordered align-middle w-100">
                    <thead class="table-dark">
                        <tr>
                            <th>FOTO</th>
                            <th>BRINCO</th>
                            <th>IDADE</th>
                            <th>LOCAL</th>
                            <th>LOTE</th>
                            <th class="no-export">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($bovino = mysqli_fetch_assoc($result)): 
                            $foto = (!empty($bovino['imagem'])) ? $bovino['imagem'] : 'uploads/imagens/001.jpg';
                        ?>
                            <tr>
                                <td>
                                    <img src="<?= $foto ?>" class="img-animal-thumb img-export" 
                                         data-bs-toggle="modal" data-bs-target="#fotoModal"
                                         onclick="ampliarFoto('<?= $foto ?>', '<?= $bovino['brinco'] ?>')">
                                </td>
                                <td><strong><?= $bovino['brinco'] ?></strong></td>
                                <td><?= $bovino['idade'] ?> m</td>
                                <td><?= $bovino['local'] ?></td>
                                <td><?= $bovino['lote'] ?></td>
                                <td class="no-export">
                                    <a href="view_animal.php?id=<?= $bovino['cod_animal'] ?>" class="btn btn-secondary btn-sm">Visualizar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fotoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModal">Visualizando Animal</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center bg-light">
                    <img src="" id="imgAmpliada">
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

  <script>
    function ampliarFoto(caminho, brinco) {
        document.getElementById('imgAmpliada').src = caminho;
        document.getElementById('tituloModal').innerText = 'Brinco: ' + brinco;
    }

    function getBase64Image(img) {
        try {
            var canvas = document.createElement("canvas");
            canvas.width = 250; 
            canvas.height = 250;
            var ctx = canvas.getContext("2d");
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(img, 0, 0, 250, 250);
            return canvas.toDataURL("image/jpeg", 0.8); 
        } catch (e) {
            return null;
        }
    }

    $(document).ready(function() {
        var semFotoBase64 = "";
        var tempImg = new Image();
        tempImg.crossOrigin = "Anonymous";
        tempImg.src = 'uploads/imagens/001.jpg'; 
        tempImg.onload = function() { 
            semFotoBase64 = getBase64Image(this); 
        };

        $('#tabelaBovinos').DataTable({
            paging: false,
            dom: 'Bfrtip',
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' },
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                    className: 'btn btn-danger btn-export',
                    exportOptions: { 
                        columns: ':not(.no-export)',
                        stripHtml: false,
                        format: {
                            body: function (data, row, column, node) {
                                return column === 0 ? data : (node.innerText || node.textContent);
                            }
                        }
                    },
                    customize: function(doc) {
                        // 1. Capturar o filtro de Lote (Atualizado)
                        var valorFiltro = $('select[name="lote"]').val();
                        var textoFiltro = (valorFiltro && valorFiltro !== "") ? 
                                          $('select[name="lote"] option:selected').text() : 
                                          "Todos os Lotes";

                        doc.pageMargins = [30, 30, 30, 30]; 

                        // 3. ADICIONAR FILTRO AO PDF
                        doc.content.unshift({
                            text: 'Lote: ' + textoFiltro,
                            fontSize: 12,
                            italics: true,
                            alignment: 'center',
                            margin: [0, 0, 0, 20]
                        });

                        var tabelaNode = doc.content.find(function(node) {
                            return node.table !== undefined;
                        });

                        if (tabelaNode) {
                            tabelaNode.table.dontBreakRows = true;
                            tabelaNode.table.widths = ['20%', '15%', '15%', '25%', '25%'];
                            
                            var imagesBase64 = [];
                            $('.img-export').each(function() {
                                if (this.src.indexOf('001.jpg') !== -1) {
                                    imagesBase64.push(semFotoBase64);
                                } else {
                                    imagesBase64.push(getBase64Image(this) || semFotoBase64);
                                }
                            });

                            for (var i = 1; i < tabelaNode.table.body.length; i++) {
                                var imgData = imagesBase64[i - 1] || semFotoBase64;
                                if (imgData) {
                                    tabelaNode.table.body[i][0] = {
                                        image: imgData,
                                        width: 80, 
                                        alignment: 'center'
                                    };
                                }
                                for (var j = 1; j < 5; j++) {
                                    tabelaNode.table.body[i][j].alignment = 'center';
                                    tabelaNode.table.body[i][j].margin = [0, 25, 0, 0];
                                }
                            }

                            tabelaNode.table.body[0].forEach(function(h) {
                                h.fillColor = '#212529'; h.color = 'white'; h.alignment = 'center';
                            });
                        }
                    }
                }
            ]
        });
    });
</script>
</body>
</html>