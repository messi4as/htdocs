<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require 'db_connect.php';
error_log("cadastrar.php: db_connect.php carregado.");
require 'funcoes_log.php';
error_log("cadastrar.php: funcoes_log.php carregado.");

// Verificar se o usuário está logado e se o ID do usuário está na sessão
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true || !isset($_SESSION['id_usuario'])) {
    error_log("cadastrar.php: Autenticação falhou ou ID de usuário não definido na sessão.");
    echo "Autenticação falhou na verificação do cadastrar.php<br>";
    var_dump($_SESSION);
    exit(0);
    // $_SESSION['mensagem'] = "Autenticação necessária.";
    // header('Location: lista_veiculos.php');
    // exit(0);
}

$id_usuario_logado = $_SESSION['id_usuario'];
error_log("cadastrar.php: ID do usuário logado: " . $id_usuario_logado);

if (isset($_POST['edit_veiculos'])) {
    error_log("cadastrar.php: Formulário de edição recebido.");
    $id = $_POST['id'];
    $nome = htmlspecialchars($_POST['nome_veiculo']);
    $placa = htmlspecialchars($_POST['placa_veiculo']);
    $renavan = htmlspecialchars($_POST['renavan_veiculo']);
    $uf = htmlspecialchars($_POST['uf_veiculo']);
    $chassi = htmlspecialchars($_POST['chassi_veiculo']);
    $marca_modelo = htmlspecialchars($_POST['marca_modelo_veiculo']);
    $proprietario = str_replace("\r\n", "<br>", mysqli_real_escape_string($conn, $_POST['proprietario_veiculo']));

    $foto_caminho = $_POST['foto_atual']; // Inicializa com a foto atual
    error_log("cadastrar.php: Foto atual: " . $foto_caminho);

    // Processar a foto
    if (isset($_FILES['foto_veiculo']) && $_FILES['foto_veiculo']['error'] == 0) {
        error_log("cadastrar.php: Nova foto enviada.");
        // Excluir a foto antiga
        if (file_exists($_POST['foto_atual']) && !empty($_POST['foto_atual'])) {
            error_log("cadastrar.php: Excluindo foto antiga: " . $_POST['foto_atual']);
            unlink($_POST['foto_atual']);
        }
        $foto_nome = uniqid() . '-' . $_FILES['foto_veiculo']['name'];
        $foto_caminho = 'uploads/fotos/' . $foto_nome;
        error_log("cadastrar.php: Caminho da nova foto: " . $foto_caminho);
        if (!move_uploaded_file($_FILES['foto_veiculo']['tmp_name'], $foto_caminho)) {
            error_log("cadastrar.php: Erro ao mover a nova foto.");
            $_SESSION['mensagem'] = "Erro ao mover a nova foto.";
            header('Location: edit_veiculos.php?id=' . $id);
            exit(0);
        }
    }

    $documentos = json_decode($_POST['documentos_atuais'], true) ?? []; // Inicializa com documentos atuais
    error_log("cadastrar.php: Documentos atuais: " . json_encode($documentos));

    // Processar os documentos
    if (isset($_POST['acao_documentos']) && $_POST['acao_documentos'] == 'substituir') {
        error_log("cadastrar.php: Ação de documentos: substituir.");
        // Excluir os documentos antigos
        if (!empty($documentos) && is_array($documentos)) {
            foreach ($documentos as $documento_atual) {
                if (file_exists($documento_atual)) {
                    error_log("cadastrar.php: Excluindo documento antigo: " . $documento_atual);
                    unlink($documento_atual);
                }
            }
            $documentos = []; // Limpa o array para adicionar os novos
        }
    }

    if (isset($_FILES['documentos_veiculo']) && is_array($_FILES['documentos_veiculo']['name'])) {
        foreach ($_FILES['documentos_veiculo']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['documentos_veiculo']['error'][$key] == 0) {
                $nome_arquivo = pathinfo($_FILES['documentos_veiculo']['name'][$key], PATHINFO_FILENAME);
                $extensao_arquivo = pathinfo($_FILES['documentos_veiculo']['name'][$key], PATHINFO_EXTENSION);
                $documento_nome = $nome_arquivo . '-' . uniqid() . '.' . $extensao_arquivo;
                $documento_caminho = 'uploads/documentos/' . $documento_nome;
                error_log("cadastrar.php: Caminho do novo documento: " . $documento_caminho);
                if (!move_uploaded_file($tmp_name, $documento_caminho)) {
                    error_log("cadastrar.php: Erro ao mover o arquivo: " . $_FILES['documentos_veiculo']['name'][$key]);
                    $_SESSION['mensagem'] = "Erro ao mover o arquivo: " . $_FILES['documentos_veiculo']['name'][$key];
                    header('Location: edit_veiculos.php?id=' . $id);
                    exit(0);
                }
                $documentos[] = $documento_caminho;
            } elseif ($_FILES['documentos_veiculo']['error'][$key] !== UPLOAD_ERR_NO_FILE) {
                error_log("cadastrar.php: Erro no upload do arquivo: " . $_FILES['documentos_veiculo']['name'][$key] . " - Código de erro: " . $_FILES['documentos_veiculo']['error'][$key]);
                $_SESSION['mensagem'] = "Erro no upload do arquivo: " . $_FILES['documentos_veiculo']['name'][$key] . " - Código de erro: " . $_FILES['documentos_veiculo']['error'][$key];
                header('Location: edit_veiculos.php?id=' . $id);
                exit(0);
            }
        }
    }

    $documentos_json = json_encode(array_unique($documentos));
    if ($documentos_json === false) {
        error_log("cadastrar.php: Erro ao codificar documentos em JSON: " . json_last_error_msg());
        $_SESSION['mensagem'] = "Erro ao codificar documentos em JSON: " . json_last_error_msg();
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
    error_log("cadastrar.php: Documentos JSON: " . $documentos_json);

    // Atualizar os dados no banco de dados
    $stmt = $conn->prepare("UPDATE veiculos SET nome_veiculo=?, placa_veiculo=?, renavan_veiculo=?, uf_veiculo=?, chassi_veiculo=?, marca_modelo_veiculo=?, proprietario_veiculo=?, foto_veiculo=?, documentos_veiculo=? WHERE cod_veiculo=?");
    if ($stmt === false) {
        error_log("cadastrar.php: Erro na preparação da query de UPDATE: " . $conn->error);
        die("Erro na preparação da query de UPDATE: " . $conn->error);
    }
    $stmt->bind_param("sssssssssi", $nome, $placa, $renavan, $uf, $chassi, $marca_modelo, $proprietario, $foto_caminho, $documentos_json, $id);

    if ($stmt->execute()) {
        error_log("cadastrar.php: Veículo editado com sucesso para ID: " . $id);
        $_SESSION['mensagem'] = "Veículo editado com sucesso!";
        header('Location: lista_veiculos.php'); // Redirecionar para a lista após sucesso
        exit(0);
    } else {
        error_log("cadastrar.php: Erro ao executar a query de UPDATE: " . $stmt->error);
        $_SESSION['mensagem'] = "Erro ao editar veículo: " . $stmt->error;
        header('Location: edit_veiculos.php?id=' . $id);
        exit(0);
    }
} else {
    error_log("cadastrar.php: Nenhum formulário de edição recebido.");
    // Se a página for acessada diretamente sem o POST do formulário de edição
    // você pode adicionar alguma lógica aqui ou simplesmente não fazer nada,
    // o que resultaria em uma página em branco (sem o formulário).
    // Se você espera que esta página seja acessada apenas via POST,
    // pode redirecionar o usuário para outro lugar.
    // Exemplo:
    // header('Location: lista_veiculos.php');
    // exit(0);
}

$conn->close();
error_log("cadastrar.php: Conexão com o banco de dados fechada.");
?>
