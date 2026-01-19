<?php
session_start();
require 'db_connect.php';

// Verifica se o método é POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recupera e sanitiza os dados
    $brincos_input = $_POST['brincos'] ?? '';
    $novo_agrupamento = $_POST['novo_agrupamento'] ?? '';
    $nome_novo_agrupamento = trim($_POST['nome_novo_agrupamento'] ?? ''); // Para a opção de criar novo

    // Define o valor final do agrupamento
    $valor_agrupamento = $novo_agrupamento;

    // Se a opção de criar novo foi selecionada E o nome foi preenchido, usa o nome do novo campo
    if ($novo_agrupamento == 'NOVO_GRUPO' && !empty($nome_novo_agrupamento)) {
        $valor_agrupamento = $nome_novo_agrupamento;
    } elseif ($novo_agrupamento == 'NOVO_GRUPO' && empty($nome_novo_agrupamento)) {
         $_SESSION['mensagem'] = "Erro: Você selecionou 'Criar Novo Grupo', mas não forneceu um nome.";
        header("Location: estratificacao.php");
        exit(0);
    }

    if (empty($brincos_input) || empty($valor_agrupamento)) {
        $_SESSION['mensagem'] = "Erro: Brincos e Agrupamento são obrigatórios.";
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
    $valor_agrupamento_safe = mysqli_real_escape_string($conn, $valor_agrupamento);

    // Assume-se que o campo de agrupamento está na tabela 'bovinos'
    $update_query = "UPDATE bovinos SET agrupamento = '{$valor_agrupamento_safe}' WHERE brinco IN ({$brinco_list}) AND status = 'ATIVO'";
    
    if (mysqli_query($conn, $update_query)) {
        $linhas_afetadas = mysqli_affected_rows($conn);
        if ($linhas_afetadas > 0) {
            $_SESSION['mensagem'] = "Sucesso: **{$linhas_afetadas}** animal(is) ativo(s) teve(ram) o agrupamento atualizado para **{$valor_agrupamento}**.";
        } else {
             $_SESSION['mensagem'] = "Aviso: Nenhum animal ativo encontrado com os brincos fornecidos para atualizar o agrupamento.";
        }
    } else {
        $_SESSION['mensagem'] = "Erro ao atualizar agrupamento: " . mysqli_error($conn);
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