// Objetivo: Importar dados de um arquivo Excel para o banco de dados atualizando o Peso do animal conforme o ID da Ocorrência.
<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexão com o banco de dados
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$linhas_atualizadas = 0;
// Verifica se o arquivo foi enviado
if (isset($_FILES['arquivo_excel'])) {
    $arquivo = $_FILES['arquivo_excel']['tmp_name'];
    try {
        $spreadsheet = IOFactory::load($arquivo);
        $sheet = $spreadsheet->getActiveSheet();
        $dados = $sheet->toArray();

        // Processar os dados da planilha
        foreach ($dados as $linha) {
            $id = $conn->real_escape_string($linha[0]);
            $peso = $conn->real_escape_string($linha[1]);
           

            // Atualizar os dados no banco de dados
            $sql = "UPDATE ocorrencias SET peso='$peso' WHERE id='$id'";
            if ($conn->query($sql)) {
                $linhas_atualizadas++;
            } else {
                echo "Erro ao atualizar dados: " . $conn->error;
            }
        }

        // Redirecionar para evitar reenvio do formulário e passar o número de linhas atualizadas
         $_SESSION['mensagem'] = "Pesos Atualizados! Total = $linhas_atualizadas Linhas Atualizadas!";
        header("Location: importar.php?update_success=true&linhas_atualizadas=$linhas_atualizadas");
        exit();
    } catch (Exception $e) {
        echo "Erro ao processar o arquivo: " . $e->getMessage();
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}

$conn->close();
?>