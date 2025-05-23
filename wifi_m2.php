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
  <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
  <script type="text/javascript" src="js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <title>WIFI-M2</title>

  <style>
    .center-content {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
    }

    .qrcode {
      margin: 20px 0;
    }
  </style>
</head>

<body>
  <?php include('navbar.php'); ?>
  <div class="container mt-4">
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>REDE: M2 SHOWS - 2.4GHz</h4>
          </div>
          <div class="card-body center-content">
            <h1>ACESSO WI-FI M2</h1>
            <div class="qrcode">
              <img src="images/qrcode.png" alt="QR Code">
            </div>
            <h1>REDE: M2 SHOWS - 2.4GHz<br>SENHA: b1J512f4@M2</h1>
          </div>
        </div>
      </div>
    </div>
  </div>
</body>

</html>