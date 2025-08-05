<?php
require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Obter dados do formulário, incluindo a nova data
    $descricao = $_POST['descricao'];
    $data_entrega = $_POST['data_entrega']; // <-- CAPTURA A DATA ENVIADA
    $entregador = $_POST['entregador'];
    $receptor = $_POST['receptor'];
    $caminho_anexo = null;

    // 2. Lógica para Upload do Anexo (continua a mesma)
    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] == UPLOAD_ERR_OK) {
        $diretorio_uploads = 'uploads/documentos/';
        if (!is_dir($diretorio_uploads)) {
            mkdir($diretorio_uploads, 0755, true);
        }
        $nome_arquivo = uniqid() . '-' . basename($_FILES["anexo"]["name"]);
        $caminho_completo = $diretorio_uploads . $nome_arquivo;
        if (move_uploaded_file($_FILES["anexo"]["tmp_name"], $caminho_completo)) {
            $caminho_anexo = $caminho_completo;
        } else {
            header("Location: protocolo_form.php?status=error&msg=" . urlencode("Falha ao mover o arquivo."));
            exit();
        }
    }

    // 3. Inserir dados no banco, incluindo a data_entrega
    // A query agora inclui o campo 'data_entrega'
    $sql = "INSERT INTO protocolos (descricao, data_entrega, entregador, receptor, caminho_anexo) VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    // O bind_param agora tem 5 parâmetros ('sssss')
    $stmt->bind_param("sssss", $descricao, $data_entrega, $entregador, $receptor, $caminho_anexo);

    if ($stmt->execute()) {
        header("Location: listar_protocolos.php?status=success");
    } else {
        $error_msg = urlencode($stmt->error);
        header("Location: listar_protocolos.php?status=error&msg=$error_msg");
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Acesso inválido.";
}
?>