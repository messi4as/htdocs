<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['nome_usuario'])) {
        $usuario = mysqli_real_escape_string($conn, $_POST['nome_usuario']);
        $senha = password_hash(mysqli_real_escape_string($conn, $_POST['senha']), PASSWORD_BCRYPT);
        $funcao = mysqli_real_escape_string($conn, $_POST['funcao']);

        // Verificar se o nome_usuario já existe na tabela usuarios
        $sql_usuario = "SELECT * FROM usuarios WHERE nome_usuario = '$usuario'";
        $result_usuario = mysqli_query($conn, $sql_usuario);

        if (mysqli_num_rows($result_usuario) > 0) {
            $_SESSION['mensagem'] = "Nome de usuário já existe!";
        } else {
            // Verificar se o funcao existe na tabela permissoes
            $sql_funcao = "SELECT * FROM permissoes WHERE funcao = '$funcao'";
            $result_funcao = mysqli_query($conn, $sql_funcao);

            if (mysqli_num_rows($result_funcao) > 0) {
                $sql = "INSERT INTO usuarios (nome_usuario, senha, funcao) VALUES ('$usuario', '$senha', '$funcao')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['mensagem'] = "Usuário criado com sucesso!";
                } else {
                    $_SESSION['mensagem'] = "Erro ao criar usuário: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['mensagem'] = "Função inválida!";
            }
        }
    } elseif (isset($_POST['funcao'])) {
        $funcao = mysqli_real_escape_string($conn, $_POST['funcao']);
        $recurso = mysqli_real_escape_string($conn, $_POST['recurso']);
        $pode_ler = isset($_POST['pode_ler']) ? 1 : 0;
        $pode_escrever = isset($_POST['pode_escrever']) ? 1 : 0;
        $pode_deletar = isset($_POST['pode_deletar']) ? 1 : 0;

        // Verificar se a funcao já existe na tabela permissoes
        $sql_funcao_existente = "SELECT * FROM permissoes WHERE funcao = '$funcao'";
        $result_funcao_existente = mysqli_query($conn, $sql_funcao_existente);

        if (mysqli_num_rows($result_funcao_existente) > 0) {
            $_SESSION['mensagem'] = "Função já existe!";
        } else {
            $sql = "INSERT INTO permissoes (funcao, recurso, pode_ler, pode_escrever, pode_deletar) VALUES ('$funcao', '$recurso', '$pode_ler', '$pode_escrever', '$pode_deletar')";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['mensagem'] = "Permissão criada com sucesso!";
            } else {
                $_SESSION['mensagem'] = "Erro ao criar permissão: " . mysqli_error($conn);
            }
        }
    }
}

// Consulta para buscar todos os usuários cadastrados
$sql_usuarios = "SELECT id_usuarios, nome_usuario FROM usuarios";
$result_usuarios = mysqli_query($conn, $sql_usuarios);
$usuarios = mysqli_fetch_all($result_usuarios, MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="pt-br">

<head>

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" href="/images/ico_m2.png" type="image/x-icon">
        <script type="text/javascript" src="/js/bootstrap.bundle.min.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <title>Criar Usuário e Permissão</title>
    </head>

<body>

    <?php include('navbar.php'); ?>
   
    <div class="container mt-4">
        <?php if (isset($_SESSION['mensagem'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['mensagem'];
                unset($_SESSION['mensagem']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                
            </div>
        <?php endif; ?>

        <div class="col-md-12">
                <div class="card">               
                    <div class="card-header">  
                    <h4>CONTROLE DE USUÁRIOS 

                    <a href="auditoria.php" class="btn btn-warning me-2 float-end"> <span class="bi bi-bar-chart"></span>&nbsp;Ver Alterações </a>
                    </h4>
                    </div>
                </div>
                <BR>

        
        <div class="row">
        
            <div class="col-md-6">
                <div class="card">               
                    <div class="card-header">                     
                  

                        <h4>Criar Usuário

                    

                        </h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="nome_usuario">Nome de Usuário:</label>
                                <input type="text" name="nome_usuario" id="nome_usuario" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="senha">Senha:</label>
                                <input type="password" name="senha" id="senha" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="funcao">Função:</label>
                                <select name="funcao" id="funcao" class="form-control" required>
                                    <?php
                                    $sql_permissoes = "SELECT DISTINCT funcao FROM permissoes";
                                    $result_permissoes = mysqli_query($conn, $sql_permissoes);
                                    while ($row_permissao = mysqli_fetch_assoc($result_permissoes)) {
                                        echo "<option value='" . $row_permissao['funcao'] . "'>" . $row_permissao['funcao'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <br>
                            <button type="submit" class="btn btn-success">Criar Usuário</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Criar Permissão</h4>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="funcao">Função:</label>
                                <input type="text" name="funcao" id="funcao" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="recurso">Recurso:</label>
                                <input type="text" name="recurso" id="recurso" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="pode_ler">Pode Ler:</label>
                                <input type="checkbox" name="pode_ler" id="pode_ler">
                            </div>
                            <div class="form-group">
                                <label for="pode_escrever">Pode Escrever:</label>
                                <input type="checkbox" name="pode_escrever" id="pode_escrever">
                            </div>
                            <div class="form-group">
                                <label for="pode_deletar">Pode Deletar:</label>
                                <input type="checkbox" name="pode_deletar" id="pode_deletar">
                            </div>
                            <br>
                            <button type="submit" class="btn btn-success">Criar Permissão</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive"> <table class="table table-striped table-bordered table-hover"  id="UsuariosTable" style="margin-top: 20px;">
            <thead>
                <tr>
                    <th colspan="2" style="text-align: center; vertical-align: middle;">USUÁRIOS CADASTRADOS</th>
                </tr>
            </thead>
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align: middle;">CÓDIGO</th>
                    <th style="text-align: center; vertical-align: middle;">USUÁRIO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($usuarios)): ?>
                    <?php foreach ($usuarios as $usuario_item): ?>
                        <tr data-id="<?= $usuario_item['id_usuarios']; ?>">
                            <td style="text-align: center; vertical-align: middle;"><?= $usuario_item['id_usuarios']; ?></td>
                            <td style="text-align: center; vertical-align: middle;"><?= $usuario_item['nome_usuario']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="2">Nenhum Usuário registrado.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>