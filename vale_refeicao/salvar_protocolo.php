<?php
require 'db_connect.php';

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Obter dados do formulário
    $descricao = $_POST['descricao'];
    $entregador = $_POST['entregador'];
    $receptor = $_POST['receptor'];
    $caminho_anexo = null; // Inicia como nulo

    // 2. Lógica para Upload do Anexo
    // Verifica se um arquivo foi enviado e se não houve erro no upload
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] == UPLOAD_ERR_OK) {
        
        // Diretório onde os arquivos serão salvos.
        // É mais seguro usar barras normais '/' que funcionam em todos os sistemas (Windows, Linux, macOS).
        $diretorio_uploads = 'uploads/documentos/';

        // Cria o diretório se ele não existir
        if (!is_dir($diretorio_uploads)) {
            mkdir($diretorio_uploads, 0755, true);
        }

        // Gera um nome de arquivo único para evitar sobreposições
        $nome_arquivo = uniqid() . '-' . basename($_FILES["anexo"]["name"]);
        $caminho_completo = $diretorio_uploads . $nome_arquivo;

        // Move o arquivo temporário para o diretório final
        if (move_uploaded_file($_FILES["anexo"]["tmp_name"], $caminho_completo)) {
            $caminho_anexo = $caminho_completo; // Salva o caminho para o banco de dados
        } else {
            // Se falhar, redireciona com mensagem de erro
            header("Location: protocolo_form.php?status=error&msg=" . urlencode("Falha ao mover o arquivo."));
            exit();
        }
    }

    // 3. Inserir dados no banco de dados usando Prepared Statements
    $sql = "INSERT INTO protocolos (descricao, entregador, receptor, caminho_anexo) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    // 'ssss' significa que estamos enviando 4 parâmetros do tipo string
    $stmt->bind_param("ssss", $descricao, $entregador, $receptor, $caminho_anexo);

    if ($stmt->execute()) {
        // Redireciona para o formulário com mensagem de sucesso
        header("Location: listar_protocolos.php?status=success");
    } else {
        // Redireciona com mensagem de erro do banco de dados
        $error_msg = urlencode($stmt->error);
        header("Location: listar_protocolos.php?status=error&msg=$error_msg");
    }

    $stmt->close();
    $conn->close();

} else {
    // Se alguém tentar acessar o script diretamente
    echo "Acesso inválido.";
}
?>