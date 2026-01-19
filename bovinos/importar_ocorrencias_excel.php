<?php
session_start();
require 'db_connect.php';
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['importar_ocorrencias']) && isset($_FILES['arquivo_excel'])) {
    $arquivo = $_FILES['arquivo_excel']['tmp_name'];
    
    try {
        $spreadsheet = IOFactory::load($arquivo);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
        
        $linhasImportadas = 0;
        $erros = [];

        // Removida a verificação de "dados abaixo do cabeçalho"
        if (empty($sheetData)) {
            throw new Exception("O arquivo está totalmente vazio.");
        }

        foreach ($sheetData as $index => $row) {
            // AGORA NÃO PULA MAIS A LINHA 1
            
            $brinco    = trim((string)$row['A']);
            $dataRaw   = trim((string)$row['B']);
            $local     = trim((string)$row['C']);
            $tipo      = trim((string)$row['D']);
            $peso      = str_replace(',', '.', trim((string)$row['E']));
            $descricao = trim((string)$row['F']);

            if (empty($brinco)) continue;

            // TRATAMENTO DE DATA (DD/MM/AAAA para SQL)
            $dataFormatoSQL = null;
            if (strpos($dataRaw, '/') !== false) {
                $partes = explode('/', $dataRaw);
                if(count($partes) == 3) {
                    $dataFormatoSQL = $partes[2] . '-' . $partes[1] . '-' . $partes[0];
                }
            } else {
                $dataFormatoSQL = $dataRaw; 
            }

            // BUSCA O ANIMAL
            $stmtBusca = $conn->prepare("SELECT cod_animal FROM bovinos WHERE brinco = ? AND status = 'ATIVO' LIMIT 1");
            $stmtBusca->bind_param("s", $brinco);
            $stmtBusca->execute();
            $res = $stmtBusca->get_result();

            if ($res->num_rows > 0) {
                $animal = $res->fetch_assoc();
                $cod_animal = $animal['cod_animal'];

                $sqlInsert = "INSERT INTO ocorrencias (cod_animal, data, local, tipo, peso, descricao) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtInsert = $conn->prepare($sqlInsert);
                $stmtInsert->bind_param("isssss", $cod_animal, $dataFormatoSQL, $local, $tipo, $peso, $descricao);
                
                if ($stmtInsert->execute()) {
                    $linhasImportadas++;
                } else {
                    $erros[] = "Erro ao inserir linha $index: " . $stmtInsert->error;
                }
            } else {
                $erros[] = "Brinco '$brinco' na linha $index não encontrado como ATIVO.";
            }
        }

        if ($linhasImportadas > 0) {
            $_SESSION['mensagem'] = "Sucesso! $linhasImportadas registros importados.";
            $_SESSION['type'] = "success";
        } else {
            $_SESSION['mensagem'] = "Falha ao importar. Erros: " . implode(" | ", $erros);
            $_SESSION['type'] = "danger";
        }

    } catch (Exception $e) {
        $_SESSION['mensagem'] = "Erro: " . $e->getMessage();
        $_SESSION['type'] = "danger";
    }

    header("Location: view_conferencia.php");
    exit();
}