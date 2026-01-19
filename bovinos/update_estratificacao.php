<?php
session_start();
require 'db_connect.php';

// Verifica se o método é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recupera e sanitiza os dados
    $brincos_input = $_POST['brincos'] ?? '';
    $nova_estratificacao = $_POST['nova_estratificacao'] ?? '';

    if (empty($brincos_input) || empty($nova_estratificacao)) {
        $_SESSION['mensagem'] = "Erro: Brincos e Nova Estratificação são obrigatórios.";
        header("Location: estratificacao.php");
        exit(0);
    }

    // 2. Processa a lista de brincos
    $brincos_array = explode(',', $brincos_input);
    // Limpa espaços em branco e sanitiza cada brinco para uso na query
    $brincos_limpos = array_map(function($b) use ($conn) {
        return "'" . mysqli_real_escape_string($conn, trim($b)) . "'";
    }, $brincos_array);

    if (empty($brincos_limpos)) {
        $_SESSION['mensagem'] = "Erro: Nenhum brinco válido foi fornecido.";
        header("Location: estratificacao.php");
        exit(0);
    }

    // 3. Monta e executa a query de atualização
    $brinco_list = implode(',', $brincos_limpos);
    $nova_estratificacao_safe = mysqli_real_escape_string($conn, $nova_estratificacao);

    $update_query = "UPDATE bovinos SET estratificacao = '{$nova_estratificacao_safe}' WHERE brinco IN ({$brinco_list}) AND status = 'ATIVO'";
    
    if (mysqli_query($conn, $update_query)) {
        $linhas_afetadas = mysqli_affected_rows($conn);
        if ($linhas_afetadas > 0) {
            $_SESSION['mensagem'] = "Sucesso: **{$linhas_afetadas}** animal(is) ativo(s) teve(ram) a estratificação atualizada para **{$nova_estratificacao}**.";
        } else {
             $_SESSION['mensagem'] = "Aviso: Nenhum animal ativo encontrado com os brincos fornecidos para atualizar.";
        }
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar estratificação: " . mysqli_error($conn);
    }

    // Fecha a conexão e redireciona
    mysqli_close($conn);
    header("Location: estratificacao.php");
    exit(0);

} else {
    // Redireciona se a requisição não for POST (acesso direto)
    header("Location: estratificacao.php");
    exit(0);
}
?>