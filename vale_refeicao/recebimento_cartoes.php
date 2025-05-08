<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cartoes = explode(',', $_POST['cartoes']);

    // Atualizar status dos cartões para 'estoque'
    foreach ($cartoes as $numero) {
        $numero = trim($numero);
        $sql = "UPDATE cartoes SET status = 'estoque' WHERE numero = '$numero' AND status = 'utilizado'";
        $conn->query($sql);
    }

    echo "Cartões recebidos com sucesso!";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Recebimento de Cartões Utilizados</title>
</head>

<body>
    <form method="POST" action="">
        <label for="cartoes">Cartões (separados por vírgulas):</label>
        <input type="text" id="cartoes" name="cartoes" required><br>
        <button type="submit">Receber Cartões</button>
    </form>
</body>

</html>