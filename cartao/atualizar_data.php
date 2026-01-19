<?php
session_start();

// 1. Incluir o arquivo de conexão com o banco de dados
// Certifique-se de que o caminho 'db_connect.php' está correto.
require 'db_connect.php';

// 2. Verificar se a requisição é do tipo POST (ou seja, se o formulário foi enviado)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =================================================================
    // 3. Obter e Sanitizar os Dados do Formulário
    // =================================================================

    // ID da parcela a ser modificada (campo oculto do modal)
    if (!isset($_POST['id_parcelamento']) || empty($_POST['id_parcelamento'])) {
        $_SESSION['mensagem'] = "Erro: ID da parcela não fornecido.";
        $_SESSION['tipo_mensagem'] = 'danger';
        header("Location: lista_compras.php"); // Redireciona para uma página segura
        exit();
    }
    $id_parcelamento = mysqli_real_escape_string($conn, $_POST['id_parcelamento']);

    // Nova data de vencimento (campo input type="date")
    $nova_data = mysqli_real_escape_string($conn, $_POST['nova_data']);

    // ID da compra (útil para o redirecionamento de volta à página correta)
    $id_compra = mysqli_real_escape_string($conn, $_POST['id_compra']);

    // =================================================================
    // 4. Montar e Executar a Query de Atualização
    // =================================================================

    // A data no formato 'YYYY-MM-DD' recebido do input 'date' é segura para o SQL.
    $sql_update = "UPDATE parcelamentos 
                   SET data_vencimento = '$nova_data' 
                   WHERE id_parcelamento = $id_parcelamento";

    if ($conn->query($sql_update) === TRUE) {
        // Sucesso na atualização
        $_SESSION['mensagem'] = "✅ Data de vencimento da parcela (ID: $id_parcelamento) atualizada para **" . date('d/m/Y', strtotime($nova_data)) . "** com sucesso!";
        $_SESSION['tipo_mensagem'] = 'success';
    } else {
        // Erro na atualização
        $_SESSION['mensagem'] = "❌ Erro ao atualizar a data: " . $conn->error;
        $_SESSION['tipo_mensagem'] = 'danger';
    }
} else {
    // 5. Caso o usuário tente acessar o arquivo diretamente sem enviar o formulário
    $_SESSION['mensagem'] = "Requisição inválida. Acesso não autorizado.";
    $_SESSION['tipo_mensagem'] = 'danger';

    // Define um valor padrão de ID de compra 0 para evitar erros no redirecionamento se $id_compra não estiver definido
    $id_compra = isset($id_compra) ? $id_compra : '';
}

// =================================================================
// 6. Fechar a conexão e Redirecionar
// =================================================================

$conn->close();

// Redirecionar de volta para a página de visualização das parcelas.
// **ATENÇÃO:** Substitua 'nome_do_seu_arquivo_de_parcelas.php' pelo nome correto do seu arquivo.
$pagina_redirecionamento = "visualizar_parcelas.php";

header("Location: $pagina_redirecionamento?id_compra=" . $id_compra);
exit();
