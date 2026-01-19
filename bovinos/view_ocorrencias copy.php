<?php
session_start();
require 'db_connect.php';

// --- CONFIGURAÇÃO DA LOGO PARA O PDF ---
// Alterado para buscar o novo arquivo logo_fazenda.png conforme solicitado
$logoPath = 'images/logo_fazenda.png'; 
$logoBase64 = '';
if (file_exists($logoPath)) {
    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
    $data = file_get_contents($logoPath);
    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
}

// Recuperar locais para o filtro
$locais_result = mysqli_query($conn, "SELECT DISTINCT local FROM ocorrencias where cod_animal in (SELECT cod_animal FROM bovinos)");
$locais = mysqli_fetch_all($locais_result, MYSQLI_ASSOC);

// Recuperar Tipos para o filtro
$tipos_result = mysqli_query($conn, "SELECT DISTINCT tipo FROM ocorrencias where cod_animal in (SELECT cod_animal FROM bovinos)");
$tipos = mysqli_fetch_all($tipos_result, MYSQLI_ASSOC);

// Recuperar Brincos para o filtro (Select2)
$brincos_result = mysqli_query($conn, "SELECT DISTINCT brinco FROM bovinos where cod_animal in (SELECT cod_animal FROM ocorrencias)");
$brincos = mysqli_fetch_all($brincos_result, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <title>OCORRÊNCIAS</title>

    <style>
        .select2-container--default .select2-selection--single { height: 38px; border: 1px solid #ced4da; border-radius: .25rem; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 36px; padding-left: .75rem; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
        
        #tabelaOcorrencias th, #tabelaOcorrencias td { text-align: center; vertical-align: middle !important; }
        .dt-buttons { margin-bottom: 15px; }
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
                            <h4>REGISTROS DE OCORRÊNCIAS <div class="float-end">
                            </h4>
                        </div>
                        <div class="card-body">
                        
                        <form method="GET" action="" class="mb-4">
                            <div class="row g-2">
                                <div class="col-md-2">
                                    <label class="small fw-bold">Data Inicial</label>
                                    <input type="date" id="data_inicial" name="data_inicial" class="form-control" value="<?= $_GET['data_inicial'] ?? '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold">Data Final</label>
                                    <input type="date" id="data_final" name="data_final" class="form-control" value="<?= $_GET['data_final'] ?? '' ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold">Local</label>
                                    <select name="local" class="form-select">
                                        <option value="">Todos</option>
                                        <?php foreach ($locais as $l): ?>
                                            <option value="<?= htmlspecialchars($l['local']) ?>" <?= (isset($_GET['local']) && $_GET['local'] == $l['local']) ? 'selected' : '' ?>><?= htmlspecialchars($l['local']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold">Tipo</label>
                                    <select id="filtro_tipo" name="tipo" class="form-select">
                                        <option value="">Todos</option>
                                        <?php foreach ($tipos as $t): ?>
                                            <option value="<?= htmlspecialchars($t['tipo']) ?>" <?= (isset($_GET['tipo']) && $_GET['tipo'] == $t['tipo']) ? 'selected' : '' ?>><?= htmlspecialchars($t['tipo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small fw-bold">Brinco</label>
                                    <select id="brinco_select" name="brinco" class="form-control">
                                        <option value="">Selecione</option>
                                        <?php foreach ($brincos as $b): ?>
                                            <option value="<?= htmlspecialchars($b['brinco']) ?>" <?= (isset($_GET['brinco']) && $_GET['brinco'] == $b['brinco']) ? 'selected' : '' ?>><?= htmlspecialchars($b['brinco']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-primary w-100" type="submit">Filtrar</button>
                                </div>
                            </div>
                        </form>

                        <?php
                        $data_inicial = $_GET['data_inicial'] ?? '';
                        $data_final = $_GET['data_final'] ?? '';
                        $local = $_GET['local'] ?? '';
                        $tipo = $_GET['tipo'] ?? '';
                        $brinco = $_GET['brinco'] ?? '';

                        $sql = "SELECT o.data, o.descricao, o.tipo, b.sexo, b.brinco, b.cod_animal, o.peso, b.local,
                                TIMESTAMPDIFF(MONTH, b.data_nascimento, o.data) AS idade
                                FROM ocorrencias o
                                INNER JOIN bovinos b ON o.cod_animal = b.cod_animal
                                WHERE 1=1 ";

                        $params = []; $types = '';
                        if ($data_inicial != '' && $data_final != '') { $sql .= " AND o.data BETWEEN ? AND ?"; $params[] = $data_inicial; $params[] = $data_final; $types .= 'ss'; }
                        if ($local != '') { $sql .= " AND b.local = ?"; $params[] = $local; $types .= 's'; }
                        if ($tipo != '') { $sql .= " AND o.tipo = ?"; $params[] = $tipo; $types .= 's'; }
                        if ($brinco != '') { $sql .= " AND b.brinco = ?"; $params[] = $brinco; $types .= 's'; }

                        $sql .= " ORDER BY o.id DESC";
                        $stmt = mysqli_prepare($conn, $sql);
                        if (!empty($params)) { mysqli_stmt_bind_param($stmt, $types, ...$params); }
                        mysqli_stmt_execute($stmt);
                        $result = mysqli_stmt_get_result($stmt);
                        $quantidade = mysqli_num_rows($result);
                        ?>

                        <div class="alert alert-info">
                            Quantidade de Ocorrências: <strong><?= number_format($quantidade, 0, ',', '.') ?></strong>
                        </div>

                        <table id="tabelaOcorrencias" class="table table-bordered table-striped w-100">
                            <thead class="table-dark">
                                <tr>
                                    <th>BRINCO</th>
                                    <th>IDADE</th>
                                    <th>SEXO</th>
                                    <th>PESO</th>
                                    <th>LOCAL</th>
                                    <th>DATA</th>
                                    <th>TIPO</th>
                                    <th>DESCRIÇÃO</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($row['brinco']) ?></strong></td>
                                        <td><?= $row['idade'] ?> m</td>
                                        <td><?= $row['sexo'] ?></td>
                                        <td><?= $row['peso'] ?></td>
                                        <td><?= $row['local'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                                        <td><?= $row['tipo'] ?></td>
                                        <td class="text-start"><?= $row['descricao'] ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
    $(document).ready(function() {
        $('#brinco_select').select2({ placeholder: 'Selecione', allowClear: true });

        $('#tabelaOcorrencias').DataTable({
            paging: false,
            dom: 'Bfrtip',
            // Isso garante que a ordem DESC do seu SQL seja respeitada
            order: [], 
            language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json' },
            buttons: [
                {
                    extend: 'pdfHtml5',
                    text: '<i class="bi bi-file-earmark-pdf"></i> Gerar PDF',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    exportOptions: { 
                        format: {
                            body: function (data, row, column, node) {
                                return node.innerText || node.textContent;
                            }
                        }
                    },
                    customize: function(doc) {
                        var dInic = $('#data_inicial').val();
                        var dFim = $('#data_final').val();
                        var tipoSel = $('#filtro_tipo').val();
                        var total = <?= $quantidade ?>;
                        var logo = '<?= $logoBase64 ?>';

                        var periodo = "Período: " + (dInic ? dInic.split('-').reverse().join('/') : "Início") + 
                                      " até " + (dFim ? dFim.split('-').reverse().join('/') : "Fim");
                        var tipoTexto = "Tipo: " + (tipoSel ? tipoSel : "Todos");

                        doc.content.unshift({
                            margin: [0, 0, 0, 20],
                            table: {
                                widths: [80, '*'],
                                body: [
                                    [
                                        {
                                            image: logo,
                                            width: 70,
                                            border: [false, false, false, false]
                                        },
                                        {
                                            stack: [
                                                { text: 'RELATÓRIO DE OCORRÊNCIAS', fontSize: 16, bold: true },
                                                { text: periodo, fontSize: 11, margin: [0, 2, 0, 0] },
                                                { text: tipoTexto, fontSize: 11 }
                                            ],
                                            alignment: 'center',
                                            margin: [-80, 5, 0, 0],
                                            border: [false, false, false, false]
                                        }
                                    ]
                                ]
                            },
                            layout: 'noBorders'
                        });

                        doc.content.push({
                            text: '\nTotal de Registros Encontrados: ' + total,
                            fontSize: 12, bold: true, alignment: 'right', margin: [0, 10, 0, 0]
                        });

                        var tableNode = doc.content.find(n => n.table !== undefined && n.table.body.length > 1);
                        if (tableNode) {
                            tableNode.table.widths = ['10%', '8%', '8%', '8%', '15%', '10%', '12%', '29%'];
                            for (var i = 1; i < tableNode.table.body.length; i++) {
                                for (var j = 0; j < 7; j++) {
                                    tableNode.table.body[i][j].alignment = 'center';
                                }
                                tableNode.table.body[i][7].alignment = 'left';
                            }
                        }
                    }
                }
            ]
        });
    });
</script>
</body>
</html>