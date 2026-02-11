<?php
include('db_connect.php');

// 1. Obtém os IDs via URL
$id = $_GET['id'] ?? null;
$caixa_id = $_GET['caixa_id'] ?? null;
$armario_origem = $_GET['armario'] ?? 1; // Captura o armário para não perder a aba

if (!$id) {
    die("ID do volume não fornecido.");
}

// 2. BUSCA A CAIXA ATUAL DO VOLUME NO BANCO
$stmt_check = $conn->prepare("SELECT caixa_id, caminho_anexo, tipo_fonte FROM volumes_arquivos WHERE id = ?");
$stmt_check->bind_param("i", $id);
$stmt_check->execute();
$resultado = $stmt_check->get_result()->fetch_assoc();

if (!$resultado) {
    die("Volume não encontrado.");
}

// 3. TRAVA DE SEGURANÇA (Repositório 145)
if ($resultado['caixa_id'] == 145) {
    echo "<script>
            alert('ERRO: Este volume está INCINERADO (ID 145). Registros permanentes não podem ser excluídos.');
            window.location.href = 'detalhes_caixa.php?id=145';
          </script>";
    exit;
}

// 4. REMOVE O ARQUIVO FÍSICO (PDF) SE EXISTIR
if ($resultado['tipo_fonte'] == 'arquivo' && !empty($resultado['caminho_anexo'])) {
    if (file_exists($resultado['caminho_anexo'])) {
        unlink($resultado['caminho_anexo']); 
    }
}

// 5. APAGA O REGISTRO DO BANCO (Aqui estava o erro)
$stmt_del = $conn->prepare("DELETE FROM volumes_arquivos WHERE id = ?");
$stmt_del->bind_param("i", $id);

// CORREÇÃO: Usar $stmt_del em vez de $stmt
if ($stmt_del->execute()) {
    // Redireciona mantendo o ID da caixa e o ARMÁRIO de origem
    header("Location: detalhes_caixa.php?id=" . $resultado['caixa_id'] . "&armario=" . $armario_origem . "&status=deleted");
    exit;
} else {
    echo "Erro ao excluir: " . $conn->error;
}
?>
