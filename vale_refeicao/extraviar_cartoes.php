<?php
require 'db_connect.php';

$mensagem_sucesso = "";
$mensagem_erro = "";

// Obter lista de responsáveis e suas informações
$responsaveis = $conn->query("SELECT DISTINCT responsavel FROM utilizacoes WHERE status_atualizado = 'entregue'");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cartoes = explode(',', $_POST['cartoes']);
    $data_utilizacao = $_POST['data_utilizacao'];
    $responsavel = $_POST['responsavel'];

    // Registrar utilização
    foreach ($cartoes as $numero) {
        $numero = trim($numero);
        $sql = "SELECT id FROM cartoes WHERE numero = '$numero' AND status = 'entregue'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $cartao_id = $row['id'];

            // Obter nome e função do responsável da entrega
            $sql = "SELECT nome, funcao FROM utilizacoes WHERE cartao_id = $cartao_id AND status_atualizado = 'entregue' ORDER BY data_utilizacao DESC LIMIT 1";
            $result = $conn->query($sql);
            $row = $result->fetch_assoc();
            $nome = $row['nome'];
            $funcao = $row['funcao'];

            $status_atualizado = 'extraviado';
            $sql = "INSERT INTO utilizacoes (cartao_id, data_utilizacao, responsavel, nome, funcao, status_atualizado) VALUES ($cartao_id, '$data_utilizacao', '$responsavel', '$nome', '$funcao', '$status_atualizado')";
            $conn->query($sql);
            $sql = "UPDATE cartoes SET status = '$status_atualizado' WHERE id = $cartao_id";
            $conn->query($sql);
            $mensagem_sucesso .= "Cartão $numero extraviado com sucesso.<br>";
        } else {
            $mensagem_erro .= "Cartão $numero não está disponível para extraviar.<br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="images/ico_fazenda.png" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>Extravio de Cartões</title>

    <style>
        .form-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            flex: 1 1 45%;
            min-width: 200px;
            margin-bottom: 10px;
        }

        .form-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .table-container {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

                            <h2>EXTRAVIO DE CARTÕES
                                <div class="float-end">

                            </h2>
                        </div>
                        <div class="card-body">

                            <form method="POST" action="">

                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="cartoes" class="form-label">Cartões (separados por vírgulas):</label>
                                        <input type="text" id="cartoes" name="cartoes" class="form-control" required><br>
                                    </div>
                                </div>

                                <div class="form-container">
                                    <div class="form-group">
                                        <label for="data_utilizacao" class="form-label">Data de Extravio:</label>
                                        <input type="date" id="data_utilizacao" name="data_utilizacao" class="form-control" required><br>
                                    </div>

                                    <div class="form-group">
                                        <label for="responsavel" class="form-label">Responsável:</label>
                                        <input type="text" id="responsavel" name="responsavel" class="form-control" required><br>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success"><span class="bi-save"></span>&nbsp;Registrar Extravio</button>
                            </form>

                            <!-- Modal de Sucesso -->
                            <div class="modal fade" id="sucessoModal" tabindex="-1" aria-labelledby="sucessoModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="sucessoModalLabel">Sucesso</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?= $mensagem_sucesso ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal de Erro -->
                            <div class="modal fade" id="erroModal" tabindex="-1" aria-labelledby="erroModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="erroModalLabel">Erro</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?= $mensagem_erro ?>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    $(document).ready(function() {
                                        <?php if (!empty($mensagem_sucesso)) : ?>
                                            $('#sucessoModal').modal('show');
                                        <?php endif; ?>
                                        <?php if (!empty($mensagem_erro)) : ?>
                                            $('#erroModal').modal('show');
                                        <?php endif; ?>
                                    });
                                </script>
</body>

</html>

<?php
$conn->close();
?>