<?php
session_start();
require 'db_connect.php';

$message = "";

// Ações de CADASTRO, EDIÇÃO e EXCLUSÃO
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Ações para FUNÇÕES ---
    if (isset($_POST['cadastrar_funcao'])) {
        $nome_funcao = $_POST['nome_funcao'];
        $descricao = $_POST['descricao'];
        $sql = "INSERT INTO Funcoes (nome_funcao, descricao) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nome_funcao, $descricao);
        if ($stmt->execute()) { $message = "Função cadastrada com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['excluir_funcao'])) {
        $id = $_POST['funcao_id'];
        $sql = "DELETE FROM Funcoes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Função excluída com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['editar_funcao'])) {
        $id = $_POST['funcao_id'];
        $nome_funcao = $_POST['nome_funcao'];
        $descricao = $_POST['descricao'];
        $sql = "UPDATE Funcoes SET nome_funcao = ?, descricao = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $nome_funcao, $descricao, $id);
        if ($stmt->execute()) { $message = "Função atualizada com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }

    // --- Ações para DEPARTAMENTOS ---
    if (isset($_POST['cadastrar_departamento'])) {
        $nome_departamento = $_POST['nome_departamento'];
        $sql = "INSERT INTO Departamentos (nome_departamento) VALUES (?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $nome_departamento);
        if ($stmt->execute()) { $message = "Departamento cadastrado com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['excluir_departamento'])) {
        $id = $_POST['departamento_id'];
        $sql = "DELETE FROM Departamentos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Departamento excluído com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['editar_departamento'])) {
        $id = $_POST['departamento_id'];
        $nome_departamento = $_POST['nome_departamento'];
        $sql = "UPDATE Departamentos SET nome_departamento = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome_departamento, $id);
        if ($stmt->execute()) { $message = "Departamento atualizado com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }

    // --- Ações para ATIVIDADES ---
    if (isset($_POST['cadastrar_atividade'])) {
        $nome_atividade = $_POST['nome_atividade'];
        $departamento_id = $_POST['departamento_id'];
        $sql = "INSERT INTO Atividades (nome_atividade, departamento_id) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $nome_atividade, $departamento_id);
        if ($stmt->execute()) { $message = "Atividade cadastrada com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['excluir_atividade'])) {
        $id = $_POST['atividade_id'];
        $sql = "DELETE FROM Atividades WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Atividade excluída com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['editar_atividade'])) {
        $id = $_POST['atividade_id'];
        $nome_atividade = $_POST['nome_atividade'];
        $departamento_id = $_POST['departamento_id'];
        $sql = "UPDATE Atividades SET nome_atividade = ?, departamento_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sii", $nome_atividade, $departamento_id, $id);
        if ($stmt->execute()) { $message = "Atividade atualizada com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    
    // --- Ações para FUNCIONÁRIOS ---
    if (isset($_POST['cadastrar_funcionario'])) {
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $funcao_id = $_POST['funcao_id'];
        $departamentos_ids = isset($_POST['departamentos']) ? $_POST['departamentos'] : [];

        // Inserir na tabela Funcionarios
        $sql = "INSERT INTO Funcionarios (nome, email, telefone, funcao_id) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $email, $telefone, $funcao_id);

        if ($stmt->execute()) {
            $funcionario_id = $conn->insert_id;
            // Inserir na tabela de junção (Funcionarios_Departamentos)
            if (!empty($departamentos_ids)) {
                $sql_insert_depto = "INSERT INTO Funcionarios_Departamentos (funcionario_id, departamento_id) VALUES (?, ?)";
                $stmt_depto = $conn->prepare($sql_insert_depto);
                foreach ($departamentos_ids as $depto_id) {
                    $stmt_depto->bind_param("ii", $funcionario_id, $depto_id);
                    $stmt_depto->execute();
                }
            }
            $message = "Funcionário cadastrado com sucesso!";
        } else {
            $message = "Erro: " . $stmt->error;
        }
    }
    if (isset($_POST['excluir_funcionario'])) {
        $id = $_POST['funcionario_id'];
        $sql = "DELETE FROM Funcionarios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) { $message = "Funcionário excluído com sucesso!"; } else { $message = "Erro: " . $stmt->error; }
    }
    if (isset($_POST['editar_funcionario'])) {
        $id = $_POST['funcionario_id'];
        $nome = $_POST['nome'];
        $email = $_POST['email'];
        $telefone = $_POST['telefone'];
        $funcao_id = $_POST['funcao_id'];
        $departamentos_ids = isset($_POST['departamentos']) ? $_POST['departamentos'] : [];
    
        // Atualiza a tabela Funcionarios
        $sql_update = "UPDATE Funcionarios SET nome = ?, email = ?, telefone = ?, funcao_id = ? WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssii", $nome, $email, $telefone, $funcao_id, $id);
        
        if ($stmt_update->execute()) {
            // Remove as associações antigas
            $sql_delete_depto = "DELETE FROM Funcionarios_Departamentos WHERE funcionario_id = ?";
            $stmt_delete_depto = $conn->prepare($sql_delete_depto);
            $stmt_delete_depto->bind_param("i", $id);
            $stmt_delete_depto->execute();
    
            // Insere as novas associações
            if (!empty($departamentos_ids)) {
                $sql_insert_depto = "INSERT INTO Funcionarios_Departamentos (funcionario_id, departamento_id) VALUES (?, ?)";
                $stmt_depto = $conn->prepare($sql_insert_depto);
                foreach ($departamentos_ids as $depto_id) {
                    $stmt_depto->bind_param("ii", $id, $depto_id);
                    $stmt_depto->execute();
                }
            }
            $message = "Funcionário atualizado com sucesso!";
        } else {
            $message = "Erro: " . $stmt_update->error;
        }
    }
}

// --- Lógica para buscar dados e preencher as tabelas ---
$funcoes = $conn->query("SELECT * FROM Funcoes ORDER BY nome_funcao ASC")->fetch_all(MYSQLI_ASSOC);
$departamentos = $conn->query("SELECT * FROM Departamentos ORDER BY nome_departamento ASC")->fetch_all(MYSQLI_ASSOC);
$atividades = $conn->query("SELECT a.id, a.nome_atividade, a.departamento_id, d.nome_departamento FROM Atividades a JOIN Departamentos d ON a.departamento_id = d.id ORDER BY a.nome_atividade ASC")->fetch_all(MYSQLI_ASSOC);
$funcionarios = $conn->query("SELECT f.id, f.nome, f.email, f.telefone, fu.nome_funcao, f.funcao_id FROM Funcionarios f JOIN Funcoes fu ON f.funcao_id = fu.id ORDER BY f.funcao_id ASC")->fetch_all(MYSQLI_ASSOC);

// Para a edição de funcionários, precisamos dos departamentos associados
function getFuncionarioDepartamentos($conn, $funcionario_id) {
    $sql = "SELECT departamento_id FROM Funcionarios_Departamentos WHERE funcionario_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $departamentos = [];
    while ($row = $result->fetch_assoc()) {
        $departamentos[] = $row['departamento_id'];
    }
    return $departamentos;
}
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
    <title>Gerenciar Organograma</title>
</head>
<body>
    <?php include("/xampp/htdocs/navbar.php"); ?>
    <div class="container mt-4">
        <?php if ($message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header"><h4>Gerenciar Funções

                <button class="btn btn-danger float-end" onclick="window.history.back();"><span class="bi-arrow-left-circle"></span>&nbsp;Voltar</button>

            </h4></div>
            <div class="card-body">
                <form action="gerenciar.php" method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="nome_funcao" placeholder="Nome da Função" required>
                        <textarea class="form-control" name="descricao" placeholder="Descrição (opcional)"></textarea>
                        <button type="submit" name="cadastrar_funcao" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
                <table class="table table-striped table-bordered">
                    <thead><tr><!--<th>ID</th>--><th>Nome</th><th>Descrição</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($funcoes as $funcao): ?>
                            <tr>
                                <!-- <td><?= htmlspecialchars($funcao['id']) ?></td> -->
                                <td><?= htmlspecialchars($funcao['nome_funcao']) ?></td>
                                <td><?= htmlspecialchars($funcao['descricao']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFuncaoModal<?= htmlspecialchars($funcao['id']) ?>"><i class="bi bi-pencil-square"></i></button>
                                    <form action="gerenciar.php" method="POST" class="d-inline-block" onsubmit="return confirm('Excluir?');">
                                        <input type="hidden" name="funcao_id" value="<?= htmlspecialchars($funcao['id']) ?>">
                                        <button type="submit" name="excluir_funcao" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editFuncaoModal<?= htmlspecialchars($funcao['id']) ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="gerenciar.php" method="POST">
                                            <div class="modal-header"><h5 class="modal-title">Editar Função</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <input type="hidden" name="funcao_id" value="<?= htmlspecialchars($funcao['id']) ?>">
                                                <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome_funcao" value="<?= htmlspecialchars($funcao['nome_funcao']) ?>" required></div>
                                                <div class="mb-3"><label class="form-label">Descrição</label><textarea class="form-control" name="descricao"><?= htmlspecialchars($funcao['descricao']) ?></textarea></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button><button type="submit" name="editar_funcao" class="btn btn-primary">Salvar</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h4>Gerenciar Departamentos</h4></div>
            <div class="card-body">
                <form action="gerenciar.php" method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="nome_departamento" placeholder="Nome do Departamento" required>
                        <button type="submit" name="cadastrar_departamento" class="btn btn-primary">Cadastrar</button>
                    </div>
                </form>
                <table class="table table-striped table-bordered">
                    <thead><tr><!--<th>ID</th>--><th>Nome</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($departamentos as $depto): ?>
                            <tr>
                               <!-- <td><?= htmlspecialchars($depto['id']) ?></td> -->
                                <td><?= htmlspecialchars($depto['nome_departamento']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editDeptoModal<?= htmlspecialchars($depto['id']) ?>"><i class="bi bi-pencil-square"></i></button>
                                    <form action="gerenciar.php" method="POST" class="d-inline-block" onsubmit="return confirm('Excluir?');">
                                        <input type="hidden" name="departamento_id" value="<?= htmlspecialchars($depto['id']) ?>">
                                        <button type="submit" name="excluir_departamento" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editDeptoModal<?= htmlspecialchars($depto['id']) ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="gerenciar.php" method="POST">
                                            <div class="modal-header"><h5 class="modal-title">Editar Departamento</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <input type="hidden" name="departamento_id" value="<?= htmlspecialchars($depto['id']) ?>">
                                                <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome_departamento" value="<?= htmlspecialchars($depto['nome_departamento']) ?>" required></div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button><button type="submit" name="editar_departamento" class="btn btn-primary">Salvar</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div> 

        <div class="card mb-4">
            <div class="card-header"><h4>Gerenciar Atividades</h4></div>
            <div class="card-body">
                <form action="gerenciar.php" method="POST" class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control" name="nome_atividade" placeholder="Nome da Atividade" required>
                        <select name="departamento_id" class="form-select" required>
                            <option value="">Selecione um Departamento</option>
                            <?php foreach ($departamentos as $depto): ?>
                                <option value="<?= htmlspecialchars($depto['id']) ?>"><?= htmlspecialchars($depto['nome_departamento']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="cadastrar_atividade" class="btn btn-primary">Cadastrar</button>
                    </div> 
                </form>
                <table class="table table-striped table-bordered">
                    <thead><tr><!-- <th>ID</th> --><th>Nome da Atividade</th><th>Departamento</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($atividades as $ativ): ?>
                            <tr>
                                <!-- <td><?= htmlspecialchars($ativ['id']) ?></td> -->
                                <td><?= htmlspecialchars($ativ['nome_atividade']) ?></td>
                                <td><?= htmlspecialchars($ativ['nome_departamento']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editAtivModal<?= htmlspecialchars($ativ['id']) ?>"><i class="bi bi-pencil-square"></i></button>
                                    <form action="gerenciar.php" method="POST" class="d-inline-block" onsubmit="return confirm('Excluir?');">
                                        <input type="hidden" name="atividade_id" value="<?= htmlspecialchars($ativ['id']) ?>">
                                        <button type="submit" name="excluir_atividade" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editAtivModal<?= htmlspecialchars($ativ['id']) ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="gerenciar.php" method="POST">
                                            <div class="modal-header"><h5 class="modal-title">Editar Atividade</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <input type="hidden" name="atividade_id" value="<?= htmlspecialchars($ativ['id']) ?>">
                                                <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome_atividade" value="<?= htmlspecialchars($ativ['nome_atividade']) ?>" required></div>
                                                <div class="mb-3">
                                                    <label class="form-label">Departamento</label>
                                                    <select name="departamento_id" class="form-select" required>
                                                        <?php foreach ($departamentos as $depto): ?>
                                                            <option value="<?= htmlspecialchars($depto['id']) ?>" <?= ($depto['id'] == $ativ['departamento_id']) ? 'selected' : '' ?>><?= htmlspecialchars($depto['nome_departamento']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button><button type="submit" name="editar_atividade" class="btn btn-primary">Salvar</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><h4>Gerenciar Funcionários</h4></div>
            <div class="card-body">
                <form action="gerenciar.php" method="POST" class="mb-4">
                    <div class="mb-3"><input type="text" class="form-control" name="nome" placeholder="Nome do Funcionário" required></div>
                    <div class="mb-3"><input type="email" class="form-control" name="email" placeholder="Email"></div>
                    <div class="mb-3"><input type="text" class="form-control" name="telefone" placeholder="Telefone"></div>
                    <div class="mb-3">
                        <label class="form-label">Função:</label>
                        <select name="funcao_id" class="form-select" required>
                            <option value="">Selecione uma Função</option>
                            <?php foreach ($funcoes as $funcao): ?>
                                <option value="<?= htmlspecialchars($funcao['id']) ?>"><?= htmlspecialchars($funcao['nome_funcao']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Departamentos:</label>
                        <div class="form-control" style="height: auto;">
                            <?php foreach ($departamentos as $depto): ?>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="departamentos[]" value="<?= htmlspecialchars($depto['id']) ?>">
                                    <label class="form-check-label"><?= htmlspecialchars($depto['nome_departamento']) ?></label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <button type="submit" name="cadastrar_funcionario" class="btn btn-primary">Cadastrar Funcionário</button>
                </form>
                <table class="table table-striped table-bordered">
                    <thead><tr><!-- <th>ID</th> --><th>Nome</th><th>Função</th><th>Ações</th></tr></thead>
                    <tbody>
                        <?php foreach ($funcionarios as $func): ?>
                            <?php $departamentos_associados = getFuncionarioDepartamentos($conn, $func['id']); ?>
                            <tr>
                                <!-- <td><?= htmlspecialchars($func['id']) ?></td> -->
                                <td><?= htmlspecialchars($func['nome']) ?></td>
                                <td><?= htmlspecialchars($func['nome_funcao']) ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editFuncModal<?= htmlspecialchars($func['id']) ?>"><i class="bi bi-pencil-square"></i></button>
                                    <form action="gerenciar.php" method="POST" class="d-inline-block" onsubmit="return confirm('Excluir?');">
                                        <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($func['id']) ?>">
                                        <button type="submit" name="excluir_funcionario" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <div class="modal fade" id="editFuncModal<?= htmlspecialchars($func['id']) ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="gerenciar.php" method="POST">
                                            <div class="modal-header"><h5 class="modal-title">Editar Funcionário</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                            <div class="modal-body">
                                                <input type="hidden" name="funcionario_id" value="<?= htmlspecialchars($func['id']) ?>">
                                                <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($func['nome']) ?>" required></div>
                                                <div class="mb-3"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($func['email']) ?>"></div>
                                                <div class="mb-3"><label class="form-label">Telefone</label><input type="text" class="form-control" name="telefone" value="<?= htmlspecialchars($func['telefone']) ?>"></div>
                                                <div class="mb-3">
                                                    <label class="form-label">Função:</label>
                                                    <select name="funcao_id" class="form-select" required>
                                                        <?php foreach ($funcoes as $funcao): ?>
                                                            <option value="<?= htmlspecialchars($funcao['id']) ?>" <?= ($funcao['id'] == $func['funcao_id']) ? 'selected' : '' ?>><?= htmlspecialchars($funcao['nome_funcao']) ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Departamentos:</label>
                                                    <div class="form-control" style="height: auto;">
                                                        <?php foreach ($departamentos as $depto): ?>
                                                            <div class="form-check form-check-inline">
                                                                <input class="form-check-input" type="checkbox" name="departamentos[]" value="<?= htmlspecialchars($depto['id']) ?>" <?= in_array($depto['id'], $departamentos_associados) ? 'checked' : '' ?>>
                                                                <label class="form-check-label"><?= htmlspecialchars($depto['nome_departamento']) ?></label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button><button type="submit" name="editar_funcionario" class="btn btn-primary">Salvar</button></div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>