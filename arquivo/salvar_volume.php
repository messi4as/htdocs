<?php
include('db_connect.php'); // Usando seu arquivo de conexão atual

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caixa_id = $_POST['caixa_id'];
    $descricao = $_POST['descricao'];
    $situacao = $_POST['situacao'];
    
    // Configuração do diretório de upload
    $diretorio_base = "uploads/caixa_" . $caixa_id . "/";
    
    // Cria a pasta da caixa se não existir
    if (!is_dir($diretorio_base)) {
        mkdir($diretorio_base, 0777, true);
    }

    $arquivo = $_FILES['anexo'];
    $nome_arquivo = time() . "_" . basename($arquivo["name"]); // Adiciona timestamp para evitar duplicatas
    $caminho_final = $diretorio_base . $nome_arquivo;

    // Validação simples de tipo de arquivo
    $tipo_arquivo = strtolower(pathinfo($caminho_final, PATHINFO_EXTENSION));
    if ($tipo_arquivo != "pdf") {
        echo "<script>alert('Apenas arquivos PDF são permitidos!'); history.back();</script>";
        exit;
    }

    if (move_uploaded_file($arquivo["tmp_name"], $caminho_final)) {
        // Sucesso no upload, agora salva no banco
        $stmt = $conn->prepare("INSERT INTO volumes_arquivos (caixa_id, descricao, situacao, caminho_anexo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $caixa_id, $descricao, $situacao, $caminho_final);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('Volume cadastrado com sucesso!');
                    window.location.href = 'cadastrar_volume.php?caixa_id=$caixa_id';
                  </script>";
        } else {
            echo "Erro ao registrar no banco: " . $conn->error;
        }
    } else {
        echo "Erro ao mover o arquivo para a pasta de destino.";
    }
}
?>