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

        <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalLabel">Autenticação Necessária</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <?php
                        if (isset($_GET['login_erro_infra'])) {
                            echo '<div class="alert alert-danger" role="alert">Usuário ou senha incorretos.</div>';
                        }
                        ?>
                        <form method="POST" action="processa_login_modal_infra.php">
                            <div class="mb-3">
                                <label for="modalUsuarioInfra" class="form-label">Usuário:</label>
                                <input type="text" class="form-control" id="modalUsuarioInfra" name="nome_usuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="modalSenhaInfra" class="form-label">Senha:</label>
                                <input type="password" class="form-control" id="modalSenhaInfra" name="senha" required>
                            </div>
                            <input type="hidden" name="redirect_url" id="redirect_url_modal">
                            <input type="hidden" name="target_blank" id="target_blank_modal">
                            <button type="submit" class="btn btn-primary">Entrar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
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

                        <div class="button-container">
                            <a href="#" class="btn btn-index d-flex align-items-center justify-content-center" style="width:300px;height:50px;" data-bs-toggle="modal" data-bs-target="#loginModal" data-redirect="/recibo/edit_emissor.php?id=2">
                                <span class="bi-search"></span>&nbsp;DADOS M2 SHOWS
                            </a>

                            <a href="#" class="btn btn-index d-flex align-items-center justify-content-center" style="width:300px;height:50px;" data-bs-toggle="modal" data-bs-target="#loginModal" data-redirect="/imoveis/lista_imoveis.php">
                                <span class="bi-search"></span>&nbsp;IMÓVEIS
                            </a>

                            <form action="/ordem_de_servico/index.php" target="_self">
                                <button type="submit" class="btn btn-index" style="width:300px;height:50px;">
                                    <span class="bi-search"></span>&nbsp;ORDEM DE SERVIÇO
                                </button>
                            </form>



                            <a href="#" class="btn btn-index d-flex align-items-center justify-content-center" style="width:300px;height:50px;" data-bs-toggle="modal" data-bs-target="#loginModal" data-redirect="organograma.php">
                                <span class="bi-search me-2"></span> ORGANOGRAMA
                            </a>

                            <a href="#" class="btn btn-index d-flex align-items-center justify-content-center" style="width:300px;height:50px;" data-bs-toggle="modal" data-bs-target="#loginModal" data-redirect="/veiculos/lista_veiculos.php">
                                <span class="bi-search"></span>&nbsp;VEÍCULOS
                            </a>

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

                            <a href="#" data-bs-toggle="modal" data-bs-target="#loginModal" data-redirect="/infra_redes.php">
                                <img src="/images/infra_ti.png" alt="Logo" class="me-2">
                            </a>


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

                    document.addEventListener('DOMContentLoaded', function() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const loginErro = urlParams.get('login_erro_infra');

                        if (loginErro === '1') {
                            const loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
                            loginModal.show();
                        }
                    });

                    const loginModalElement = document.getElementById('loginModal');
                    if (loginModalElement) {
                        loginModalElement.addEventListener('show.bs.modal', event => {
                            const relatedTarget = event.relatedTarget;
                            const redirectUrl = relatedTarget.getAttribute('data-redirect');
                            const targetBlank = relatedTarget.getAttribute('data-target-blank');

                            const redirectUrlInput = loginModalElement.querySelector('#redirect_url_modal');
                            const targetBlankInput = loginModalElement.querySelector('#target_blank_modal');

                            if (redirectUrlInput) {
                                redirectUrlInput.value = redirectUrl;
                            }
                            if (targetBlankInput) {
                                targetBlankInput.value = targetBlank === 'true' ? 'true' : '';
                            }
                        });
                    }
                </script>
            </div>
        </div>
    </div>
</body>

</html>