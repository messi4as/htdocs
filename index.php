<?php
session_start();
require 'db_connect.php';
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
  <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="/css/style.css">

  <title>INTRANET</title>

</head>

<body style="background-image: url('/images/LOGO ESCRITÓRIO1.png'); background-repeat: no-repeat; background-size: contain; background-position: center 10%; background-attachment: fixed;">

  <?php include('navbar.php'); ?>
  <div class="container mt-4">
    <?php include('mensagem.php'); ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">
            <h4>GESTÃO ESCRITÓRIO M2 SHOWS PRODUÇÕES


            </h4>

          </div>

          <div class="card-body">

            <div class="button-container">

              <div style="float: left;">
                <a href="/index.php">
                  <img src="/images/logo_m2.png" alt="Logo" class="me-2"> </a>
              </div>


              <div style="float: right;">
                <a href="/index.php">
                  <img src="/images/logo_fazenda.png" alt="Logo" class="me-2"> </a>
              </div>
            </div>
            <br>

            <!-- Contêiner para alinhar os botões -->

            <div class="button-container">
              <form action="/recibo/edit_emissor.php?id=2" target="_blank" onsubmit="window.open(this.action, '_blank'); return false;">
                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                  <span class="bi-search"></span>&nbsp;DADOS M2 SHOWS
                </button>
              </form>

              <form action="/imoveis/lista_imoveis.php" target="_self">
                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                  <span class="bi-search"></span>&nbsp;IMÓVEIS
                </button>
              </form>

              <form action="/ordem_de_servico/index.php" target="_self">
                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                  <span class="bi-search"></span>&nbsp;ORDEM DE SERVIÇO
                </button>
              </form>


              <form action="/organograma.php" target="_blank">
                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                  <span class="bi-search"></span>&nbsp; ORGANOGRAMA
                </button>
              </form>

              <form action="/veiculos/lista_veiculos.php" target="_self">
                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                  <span class="bi-search"></span>&nbsp;VEÍCULOS
                </button>
              </form>

              <form action="/recibo/index.php" target="_self">
                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                  <span class="bi-search"></span>&nbsp;RECIBOS
                </button>
              </form>



            </div>
            <br>

            <div style="float: left;">
              <a href="/wifi_m2.php" target="_blank">
                <img src="/images/WIFI_LOGO.png" alt="Logo" class="me-2"> </a>
            </div>

            <div style="float: right;">

              <a href="/infra_redes.php" target="_blank">
                <img src="/images/infra_ti.png" alt="Logo" class="me-2"> </a>


            </div>





          </div>
        </div>
        <audio autoplay loop style="display: none;">
          <source src="/images/ReelAudio-5797" type="audio/mpeg">
          Seu navegador não suporta o elemento de áudio.
        </audio>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var audio = document.getElementById('musicaDeFundo');
            audio.play().catch(function(error) {
                console.error('Erro ao tentar reproduzir automaticamente (oculto):', error);
                // Se a reprodução automática falhar, você pode considerar
                // exibir uma mensagem sutil ou um botão de play em outro lugar,
                // embora com controles ocultos isso seja mais desafiador.
            });
        });
    </script>
      </div>
    </div>
</body>

</html>