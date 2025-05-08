<?php

require __DIR__ . '/vendor/autoload.php';

use PHPJasper\PHPJasper;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Recebe o cod_os da URL
$os_codigo = isset($_GET['cod_rel']) ? $_GET['cod_rel'] : 1; // Valor padrão 1 se o cod_os não for fornecido

$nome = 'OS_MARAISA_';
date_default_timezone_set('America/Sao_Paulo');
$filename = $nome . date("dmY His");
$input = realpath('C:\xampp\htdocs\ordem_de_servico\relatorio\maraisa.jrxml');
$output = realpath('C:\xampp\htdocs\ordem_de_servico\relatorio') . DIRECTORY_SEPARATOR . $filename;

$options = [
    'format' => ['pdf'],
    'locale' => 'pt_BR',
    'params' => ['cod_rel' => $os_codigo], // Passa o cod_os como parâmetro
    'db_connection' => [
        'driver' => 'mysql',
        'username' => 'root',
        'host' => 'localhost',
        'database' => 'escritorio_m2',
        'port' => '3306'
    ]
];

$jasper = new PHPJasper;

$jasper->process(
    $input,
    $output,
    $options
)->execute();

// Enviar o relatório gerado como resposta HTTP
$pdfFile = $output . '.pdf';

if (file_exists($pdfFile)) {
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . basename($pdfFile) . '"');
    header('Content-Transfer-Encoding: binary');
    header('Accept-Ranges: bytes');
    readfile($pdfFile);

    // Apagar o arquivo após enviá-lo
    unlink($pdfFile);
} else {
    echo "Erro ao gerar o relatório.";
}
