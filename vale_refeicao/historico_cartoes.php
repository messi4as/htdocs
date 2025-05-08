<?php
require 'db_connect.php';

$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$nome = isset($_GET['nome']) ? $_GET['nome'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// Consulta para obter o histórico de utilização dos cartões com filtros de data, nome e status
$sql = "SELECT c.numero, u.nome, u.funcao, u.data_utilizacao, u.responsavel, u.status_atualizado
        FROM utilizacoes u
        JOIN cartoes c ON u.cartao_id = c.id
        WHERE 1=1";

if ($data_inicio && $data_fim) {
    $sql .= " AND u.data_utilizacao BETWEEN '$data_inicio' AND '$data_fim'";
}

if ($nome) {
    $sql .= " AND u.nome LIKE '%$nome%'";
}

if ($status) {
    $sql .= " AND u.status_atualizado = '$status'";
}

$result = $conn->query($sql);
$quantidade = mysqli_num_rows($result);

// Consulta para obter os nomes únicos
$nomes_result = $conn->query("SELECT DISTINCT nome FROM utilizacoes");

// Consulta para obter os status únicos
$status_result = $conn->query("SELECT DISTINCT status_atualizado FROM utilizacoes");
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>Histórico de Utilização de Cartões</title>

    <style>
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

</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="table-container">
                        <div class="card-header">

                            <h2>HISTÓRICO DE UTILIZAÇÃO
                                <div class="float-end">

                            </h2>
                        </div>

                        <div class="card-body">

                            <div class="alert alert-info" role="alert">
                                Quantidade de Registros: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>

                            <!-- Filtros -->
                            <form method="GET" action="" class="mb-3">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label for="data_inicio">Data Início:</label>
                                        <input type="date" id="data_inicio" name="data_inicio" class="form-control" value="<?= $data_inicio ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="data_fim">Data Fim:</label>
                                        <input type="date" id="data_fim" name="data_fim" class="form-control" value="<?= $data_fim ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="nome">Nome:</label>
                                        <select id="nome" name="nome" class="form-control">
                                            <option value="">Todos</option>
                                            <?php while ($row = $nomes_result->fetch_assoc()): ?>
                                                <option value="<?= $row['nome'] ?>" <?= $nome == $row['nome'] ? 'selected' : '' ?>><?= $row['nome'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="status">Status:</label>
                                        <select id="status" name="status" class="form-control">
                                            <option value="">Todos</option>
                                            <?php while ($row = $status_result->fetch_assoc()): ?>
                                                <option value="<?= $row['status_atualizado'] ?>" <?= $status == $row['status_atualizado'] ? 'selected' : '' ?>><?= $row['status_atualizado'] ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-primary form-control">Filtrar</button>
                                    </div>
                                </div>
                            </form>

                            <!-- Tabela de Histórico de Utilização -->
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="text-align: center;">NÚMERO</th>
                                        <th style="text-align: center;">NOME DO RECEBEDOR</th>
                                        <th style="text-align: center;">FUNÇÃO / SERVIÇO</th>
                                        <th style="text-align: center;">DATA</th>
                                        <th style="text-align: center;">RESPONSÁVEL</th>
                                        <th style="text-align: center;">STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<tr>";
                                            echo "<td style='text-align: center; vertical-align: middle;''>" . $row['numero'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;''>" . $row['nome'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;''>" . $row['funcao'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . date('d/m/Y', strtotime($row['data_utilizacao'])) . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['responsavel'] . "</td>";
                                            echo "<td style='text-align: center; vertical-align: middle;'>" . $row['status_atualizado'] . "</td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='6'>tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="alert alert-info" role="alert">
                                Quantidade de Registros: <?php echo number_format($quantidade, 0, ',', '.'); ?>
                            </div>
                        </div>
</body>

</html>

<?php
$conn->close();
?>