<?php
session_start();
require 'db_connect.php'; // Inclua sua conexão com o banco de dados

// Nome da sua página de listagem de parcelas (certifique-se de que está correto!)
$pagina_redirecionamento = "visualizar_parcelas.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 1. Obter e sanitizar os dados
    $id_parcelamento = mysqli_real_escape_string($conn, $_POST['id_parcelamento']);
    $id_compra = mysqli_real_escape_string($conn, $_POST['id_compra']);

    // Usamos floatval para garantir que os valores sejam numéricos e lidamos com a sanitização
    $novo_valor1_float = floatval(str_replace(',', '.', $_POST['novo_valor1']));
    $novo_valor2_float = floatval(str_replace(',', '.', $_POST['novo_valor2']));

    // Sanitizar para uso na query
    $novo_valor1 = mysqli_real_escape_string($conn, $novo_valor1_float);
    $novo_valor2 = mysqli_real_escape_string($conn, $novo_valor2_float);

    // 2. Montar e executar a query de atualização
    $sql_update = "UPDATE parcelamentos 
                   SET valor_parcela_responsavel1 = $novo_valor1, 
                       valor_parcela_responsavel2 = $novo_valor2 
                   WHERE id_parcelamento = $id_parcelamento";

    if ($conn->query($sql_update) === TRUE) {
        $_SESSION['mensagem'] = "✅ Valores da parcela (ID: $id_parcelamento) atualizados com sucesso!";
        $_SESSION['tipo_mensagem'] = 'success';
    } else {
        $_SESSION['mensagem'] = "❌ Erro ao atualizar os valores: " . $conn->error;
        $_SESSION['tipo_mensagem'] = 'danger';
    }
} else {
    $_SESSION['mensagem'] = "Requisição inválida. Acesso não autorizado.";
    $_SESSION['tipo_mensagem'] = 'danger';
}

$conn->close();

// 3. Redirecionar de volta para a página de visualização das parcelas
$id_compra = isset($id_compra) ? $id_compra : ''; // Garante que $id_compra está definido para o redirecionamento
header("Location: $pagina_redirecionamento?id_compra=" . $id_compra);
exit();
