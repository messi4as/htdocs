<?php
include('db_connect.php');

$volume_id = $_POST['volume_id'] ?? null;
$origem_id = $_POST['caixa_origem_id'] ?? null;
$destino_id = $_POST['nova_caixa_id'] ?? null;

if (!$volume_id || !$destino_id) {
    die("Dados incompletos para a transferência.");
}

// 1. CONSULTA DE SEGURANÇA
$stmt_check = $conn->prepare("SELECT caixa_id, caminho_anexo, tipo_fonte FROM volumes_arquivos WHERE id = ?");
$stmt_check->bind_param("i", $volume_id);
$stmt_check->execute();
$vol_atual = $stmt_check->get_result()->fetch_assoc();

if (!$vol_atual) {
    die("Volume não encontrado.");
}

// TRAVA DE SEGURANÇA: Repositório Digital (145)
if ($vol_atual['caixa_id'] == 145) {
    die("Erro Crítico: Volumes no Repositório Digital (145) são permanentes e não podem ser movidos.");
}

$novo_caminho = $vol_atual['caminho_anexo'];

// 2. LÓGICA DE MOVIMENTAÇÃO DE ARQUIVO FÍSICO
if ($vol_atual['tipo_fonte'] == 'arquivo' && !empty($vol_atual['caminho_anexo'])) {
    $diretorio_destino = "uploads/caixa_$destino_id/";
    
    // Cria a pasta da nova caixa se não existir
    if (!is_dir($diretorio_destino)) {
        mkdir($diretorio_destino, 0777, true);
    }
    
    $nome_arquivo = basename($vol_atual['caminho_anexo']);
    $novo_caminho = $diretorio_destino . $nome_arquivo;
    
    // Move o arquivo fisicamente no servidor
    if (file_exists($vol_atual['caminho_anexo'])) {
        rename($vol_atual['caminho_anexo'], $novo_caminho);
    }
}

// 3. ATUALIZAÇÃO NO BANCO DE DADOS
$stmt_update = $conn->prepare("UPDATE volumes_arquivos SET caixa_id = ?, caminho_anexo = ? WHERE id = ?");
$stmt_update->bind_param("isi", $destino_id, $novo_caminho, $volume_id);

if ($stmt_update->execute()) {
    // ESTA É A PARTE QUE ESTAVA A FALTAR:
    // Redireciona de volta para a caixa de origem com um status de sucesso
    header("Location: detalhes_caixa.php?id=" . $origem_id . "&status=transferido");
    exit; 
} else {
    echo "Erro ao atualizar banco: " . $conn->error;
}
?>