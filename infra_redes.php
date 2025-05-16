<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: /index.php"); // Redireciona para a página inicial se não estiver logado
    exit();
}

// O restante do seu código da página /infra_redes.php continua aqui
?>
<!doctype html>
<html lang="pt-br">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <title>INFRA REDES</title>

    <style>
        .center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
    </style>

    <style type="text/css" media="print">
        body {
            font-family: Arial, sans-serif;
            font-size: 12pt;
        }

        .card {
            border: 1px solid #ccc !important;
            margin-bottom: 1em;
            page-break-inside: avoid; /* Tenta evitar quebras de página dentro do card */
        }

        .card-header {
            background-color: #f0f0f0 !important;
            color: #000 !important;
            padding: 0.5em;
            text-align: center;
            font-weight: bold;
            border-bottom: 1px solid #ccc !important;
        }

        .card-body {
            padding: 1em;
            text-align: center;
        }

        .center-content h1 {
            font-size: 1.5em;
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .center-content img {
            max-width: 100%; /* Garante que a imagem não ultrapasse a largura da página */
            height: auto;
        }

       

        .container {
            width: 100% !important;
            margin-top: 0 !important;
            padding: 0 !important;
        }

        .row {
            display: block !important; /* Força os elementos da linha a se comportarem como blocos */
            width: 100% !important;
        }

        .col-md-14 {
            width: 100% !important; /* Ocupa toda a largura disponível */
            flex: 0 0 100% !important;
            max-width: 100% !important;
        }
    </style>
</head>

<body>
    <?php include('navbar.php'); ?>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-14">
                <div class="card">
                    <div class="card-header">
                        <h4>INFRAESTRURURA DE REDES DO ESCRITÓRIO M2</h4>
                    </div>
                    <div class="card-body center-content">
                        <h1>PLANTA DO ESCRITÓRIO</h1>
                        <img src="images/planta_escritorio.png" style="max-width: 80%;">
                        <h1>SALAS 514 A 516</h1>
                    </div>
                </div>
                <br>

                <div class="card">
                    <div class="card-header">
                        <h4>TOPOLOGIA DO ESCRITÓRIO M2</h4>
                    </div>
                    <div class="card-body center-content">
                        <h1>TOPOLOGIA</h1>
                        <img src="images/topologia.png" style="max-width: 80%;">
                        <h1></h1>
                    </div>
                </div>
                <br>

                <div class="card">
                    <div class="card-header">
                        <h4>RACK DO ESCRITÓRIO M2</h4>
                    </div>
                    <div class="card-body center-content">
                        <h1>RACK DO ESCRITÓRIO</h1>
                        <img src="images/rack.png" style="max-width: 80%;">
                        <h1></h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>