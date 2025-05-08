// Objetivo: Importar dados de um arquivo Excel para o banco de dados atualizando os registros comprovantes e documentos.
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

if (isset($_FILES['arquivo_excel'])) {
    $arquivo = $_FILES['arquivo_excel']['tmp_name'];
    try {
        $spreadsheet = IOFactory::load($arquivo);
        $sheet = $spreadsheet->getActiveSheet();
        $dados = $sheet->toArray();

        // Processar os dados da planilha
        foreach ($dados as $linha) {
            $cod_financeiro = $conn->real_escape_string($linha[0]);
            $comprovantes = $conn->real_escape_string($linha[1]);
            $documentos = $conn->real_escape_string($linha[2]);

            // Atualizar os dados no banco de dados
            $sql = "UPDATE financeiro SET comprovante='$comprovantes', documento='$documentos' WHERE cod_financeiro='$cod_financeiro'";
            if ($conn->query($sql)) {
                $linhas_atualizadas++;
            } else {
                echo "Erro ao atualizar dados: " . $conn->error;
            }
        }

        // Redirecionar para evitar reenvio do formulário e passar o número de linhas atualizadas
        header("Location: index.php?update_success=true&linhas_atualizadas=$linhas_atualizadas");
        exit();
    } catch (Exception $e) {
        echo "Erro ao processar o arquivo: " . $e->getMessage();
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}

$conn->close();
?>