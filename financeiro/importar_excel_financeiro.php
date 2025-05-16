// Objetivo: Importar dados de uma planilha Excel para o banco de dados aimentando a tabela financeiro.
<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Conexão com o banco de dados
// Usando a conexão do db_connect.php
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$linhas_importadas = 0;

if (isset($_FILES['arquivo_excel'])) {
    $arquivo = $_FILES['arquivo_excel']['tmp_name'];
    try {
        $spreadsheet = IOFactory::load($arquivo);
        $sheet = $spreadsheet->getActiveSheet();
        $dados = $sheet->toArray();

        // Processar os dados da planilha
        foreach ($dados as $linha) {
            $data = $conn->real_escape_string($linha[0]);
            $descricao = $conn->real_escape_string($linha[1]);
            $responsavel = $conn->real_escape_string($linha[2]);
            $valor = $conn->real_escape_string($linha[3]);
            $forma_pagamento = $conn->real_escape_string($linha[4]);
            $tipo = $conn->real_escape_string($linha[5]);

            // Inserir os dados no banco de dados
            $sql = "INSERT INTO financeiro (data, descricao, responsavel, valor, forma_pagamento, tipo) VALUES ('$data', '$descricao', '$responsavel', '$valor', '$forma_pagamento', '$tipo')";
            if ($conn->query($sql)) {
                $linhas_importadas++;
            } else {
                echo "Erro ao inserir dados: " . $conn->error;
            }
        }

        // Redirecionar para evitar reenvio do formulário e passar o número de linhas importadas
           $_SESSION['mensagem'] = "Financeiro Importado! Total = $linhas_atualizadas Linhas Importadas!";
        header("Location: importar.php?import_success=true&linhas_importadas=$linhas_importadas");
        exit();
    } catch (Exception $e) {
        echo "Erro ao processar o arquivo: " . $e->getMessage();
    }
} else {
    echo "Nenhum arquivo foi enviado.";
}

$conn->close();
