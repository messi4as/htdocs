<?php
include('db_connect.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $caixa_id    = $_POST['caixa_id'];
    $descricao   = $_POST['descricao'];
    $tipo_fonte  = $_POST['tipo_fonte']; 
    $responsavel = $_POST['responsavel'];
    
    $data_inicio = !empty($_POST['data_inicio']) ? $_POST['data_inicio'] : null;
    $data_fim    = !empty($_POST['data_fim'])    ? $_POST['data_fim']    : null;
    
    $caminho_anexo = null;
    $link_externo  = null; // Não precisamos mais receber via POST
    $situacao      = 'Digitalizado';

    if ($tipo_fonte == 'arquivo') {
        if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
            $diretorio_base = "uploads/caixa_" . $caixa_id . "/";
            if (!is_dir($diretorio_base)) mkdir($diretorio_base, 0777, true);

            $arquivo_nome = $_FILES['anexo']['name'];
            $novo_nome = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", $arquivo_nome);
            $caminho_final = $diretorio_base . $novo_nome;

            if (move_uploaded_file($_FILES['anexo']['tmp_name'], $caminho_final)) {
                $caminho_anexo = $caminho_final;
            }
        }
    } 
    
    // Removida a verificação obrigatória de link_externo aqui, 
    // pois o link agora é montado dinamicamente no detalhes_caixa.php
    else if ($tipo_fonte == 'link') {
        if (empty($data_inicio) || empty($data_fim)) {
            echo "<script>alert('Erro: Para links do financeiro, as datas são obrigatórias.'); history.back();</script>";
            exit;
        }
    }

    $sql = "INSERT INTO volumes_arquivos 
            (caixa_id, descricao, situacao, tipo_fonte, link_externo, caminho_anexo, responsavel, data_inicio, data_fim) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", 
        $caixa_id, $descricao, $situacao, $tipo_fonte, $link_externo, $caminho_anexo, $responsavel, $data_inicio, $data_fim
    );
     $caixa_id = $_POST['caixa_id']; // O modal envia como caixa_id
    $armario_origem = $_POST['armario_origem'] ?? 1;

   if ($stmt->execute()) {
        // Redireciona de volta para os detalhes com os parâmetros corretos
        header("Location: detalhes_caixa.php?id=$caixa_id&armario=$armario_origem&status=vol_success");
        exit;
    } else {
        echo "Erro: " . $conn->error;
    }
}
?>