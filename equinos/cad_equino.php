<?php
session_start();
require 'db_connect.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Novo Equino - Fazenda Rosada</title>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h4>CADASTRAR NOVO EQUINO
                    <a href="index.php" class="btn btn-danger float-end"><span class="bi-arrow-left"></span> Voltar</a>
                </h4>
            </div>
            <div class="card-body">
                <form action="code_equino.php" method="POST" enctype="multipart/form-data">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Nome do Animal</label>
                            <input type="text" name="nome_animal" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Proprietário</label>
                            <input type="text" name="proprietario" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Nº de Registro</label>
                            <input type="text" name="num_registro" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Raça</label>
                            <input type="text" name="raca" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Pelagem</label>
                            <input type="text" name="pelagem" class="form-control">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label>Sexo</label>
                            <select name="sexo" class="form-control">
                                <option value="Macho">Macho</option>
                                <option value="Fêmea">Fêmea</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Data de Nascimento</label>
                            <input type="date" name="data_nascimento" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Local / Pasto</label>
                            <input type="text" name="local" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="ATIVO">ATIVO</option>
                                <option value="VENDIDO">VENDIDO</option>
                                <option value="MORTE">MORTE</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Fotos do Animal (Pode selecionar várias)</label>
                            <input type="file" name="fotos[]" class="form-control" multiple>
                            <small class="text-muted">A primeira foto será a galeria inicial. Pode definir a capa na tela de visualização.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Descrição Geral</label>
                            <textarea name="descricao_geral" class="form-control" rows="1"></textarea>
                        </div>
                    </div>

                    <div class="mb-3">
                        <button type="submit" name="save_equino" class="btn btn-primary">
                            <span class="bi-save"></span> Salvar Equino
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>